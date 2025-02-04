<?php

namespace App\Controller;



use App\Cinetpay\CinetPay;
use App\Entity\Order;
use App\Entity\UserBord;
use App\Repository\BordRepository;
use App\Repository\OrderRepository;
use App\Repository\UserBordRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class PayController extends AbstractController
{
    public $marchand = array(
        "apikey" => "35602895962e889e9d3c615.93309951", // Enrer votre apikey
        "site_id" => "879072", //Entrer votre site_ID
        "secret_key" => "39004195262e99092a412c9.42980269" //Entrer votre clé secret
    );

    #[Route('/cinetpay/action', name: 'app_pay_cinetpay_action', methods: ['GET'])]
    public function action(EntityManagerInterface $manager, Security $security, request $request, SessionInterface $session, OrderRepository $command): JsonResponse
    {
        $user = $security->getUser();
        $paiement = $session->get('paiement');
        // La class gère la table "Commande"( A titre d'exemple)
        $order = new Order();
        try {
            $customer_name = $paiement['nom'];
            $customer_surname = $paiement['prenom'];
            $description = $paiement['description'];
            $amount = $paiement['prix'];
            $currency = "XAF";

            //transaction id
            $id_transaction = date("YmdHis"); // or $id_transaction = Cinetpay::generateTransId()

            //Veuillez entrer votre apiKey
            $apikey = $this->marchand["apikey"];
            //Veuillez entrer votre siteId
            $site_id = $this->marchand["site_id"];

            //notify url
            $notify_url = PayController::getCurrentUrl().'bay_book';
            //return url
            $return_url = PayController::getCurrentUrl().'bay_book';
            $channels = "ALL";

            //
            $formData = array(
                "transaction_id"=> $id_transaction,
                "amount"=> $amount,
                "currency"=> $currency,
                "customer_surname"=> $customer_name,
                "customer_name"=> $customer_surname,
                "description"=> $description,
                "notify_url" => $return_url,
                "return_url" => $return_url,
                "channels" => $channels,
                "metadata" =>  json_encode($json = [
                    'book' => $paiement['book'],
                    'user' => $user->getId(),
                    'temps' => $paiement['temps'],
                    'prix' => $paiement['prix']
                ])  , // utiliser cette variable pour recevoir des informations personnalisés.
                "alternative_currency" => "",//Valeur de la transaction dans une devise alternative
                //pour afficher le paiement par carte de credit
                "customer_email" => "", //l'email du client
                "customer_phone_number" => "", //Le numéro de téléphone du client
                "customer_address" => "", //l'adresse du client
                "customer_city" => "", // ville du client
                "customer_country" => "",//Le pays du client, la valeur à envoyer est le code ISO du pays (code à deux chiffre) ex : CI, BF, US, CA, FR
                "customer_state" => "", //L’état dans de la quel se trouve le client. Cette valeur est obligatoire si le client se trouve au États Unis d’Amérique (US) ou au Canada (CA)
                "customer_zip_code" => "" //Le code postal du client
            );
            // enregistrer la transaction dans votre base de donnée
            /*  $commande->create(); */

            $CinetPay = new CinetPay($site_id, $apikey);
            $result = $CinetPay->generatePaymentLink($formData);

            if ($result["code"] == '201')
            {
                $url = $result["data"]["payment_url"];

                $order->setTransactionId($id_transaction)
                ->setStatus(0)
                ->setNumb(1);
                $manager->persist($order);

                $manager->flush();
                //redirection vers l'url de paiement
                //dd($url);

                //return $this->redirect($url);

                return $this->json($url);




            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $this->json($url);
    }

    #[Route('/cinetpay/bay_book', name: 'bay_book', methods: ['POST'])]
    public function bay_book(request $request, OrderRepository $orderRepository, Security $security,BordRepository $bordRepository, UserRepository $userRepository, UserBordRepository $userBordRepository, EntityManagerInterface $manager){
        //$csrf_token = $request->session()->token();
        //$request->input('csrf-token', $_token);
        //dd(auth()->user(), $_token);
        //$token = csrf_token();
        //$request->query('csrf-token', $token);
        //$header = $request->header('X-XSRF-TOKEN');
        $token = $security->getToken();
        $_POST['_token'] = $token;


        if (isset($_POST['cpm_trans_id']) || isset($_POST['transaction_id']) || isset($_POST['token'])) {
            //La classe commande correspond à votre colonne qui gère les transactions dans votre base de données

            $id_transaction = $_POST['cpm_trans_id']??$_POST['transaction_id'];
            //verifier si le payement est bon dans ma base de donné
            //On recupère le statut de la transaction dans la base de donnée

            $order = $orderRepository->findOneBy(
                [
                    'transaction_id'=> $id_transaction
                ]
            );
            $numb = $order->getNumb() + 1;
            $order->setNumb($numb);
            $manager->flush();


            $VerifyStatusCmd = $order->isStatus();
            if ($VerifyStatusCmd == 1) {
                // La commande a été déjà traité
                // Arret du script
                return $this->redirect($request->headers->get('referer'));
            }

            try {
                // Verification d'etat de transaction chez CinetPay
                $CinetPay = new CinetPay($this->marchand["site_id"], $this->marchand["apikey"]);

                $CinetPay->getPayStatus($id_transaction, $this->marchand["site_id"]);
                $message = $CinetPay->chk_message;
                $code = $CinetPay->chk_code;
                $metadata = $CinetPay->chk_metadata;

                /*
                json_encode($json = [
                    'book' => $paiement['book'],
                    'user' => $user->getId(),
                    'temps' => $paiement['temps'],
                    'prix' => $paiement['prix']
                ])
                */

                $donnes_bord = json_decode($metadata, true);

                //recuperer les info du clients pour personnaliser les reponses.
                /* $commande->getUserByPayment(); */

                // redirection vers une page en fonction de l'état de la transaction
                if ($code == '00') {
                    //enregistrement du nouveau statut de la transaction dans la base de donné
                    $order->setStatus(1);

                    $id_bord = $donnes_bord['book'];
                    $id_user = $donnes_bord['user'];
                    $achat_time = $donnes_bord['temps'];

                    //le bord
                    $bord = $bordRepository->find($id_bord);

                    //user
                    $user = $userRepository->find($id_user);

                    //basket
                    $basket = $userBordRepository->findOneBy(
                        [
                            'bord'=>$bord,
                            'user'=> $user,
                        ]
                    );

                    if(!empty($bord)){
                        // Date actuelle en DateTimeImmutable
                        $date_actuelle = new \DateTimeImmutable();

                        // Tableau des durées à ajouter
                        $table_time = [
                            '1 jour' => '+3 days',
                            '3 jours' => '+3 days',
                            '1 semaine' => '+7 days',
                            '1 mois' => '+1 month',
                            '3 mois' => '+3 months',
                            '1 an' => '+1 year'
                        ];
                        // Définir la durée d'achat (par exemple, '1 semaine')
                        $date_limite = $date_actuelle->modify($table_time[$achat_time]);

                        //verifier si il avec d'abord eu a ouvrir le bord
                        if(!empty($basket)){
                            //verifier si l'utilisateur avait eu à payer le bord et verifier le temps restant pour l'ajouter sur la durée souscrit
                            if($basket->getEndAt() > $date_actuelle){

                                // Ajouter le temps restant
                                $endAt = $basket->getEndAt();
                                $date_restant = $endAt->diff($date_actuelle);

                                // Ajouter le temps restant à la nouvelle date limite
                                $date_limite = $date_limite
                                    ->modify("+{$date_restant->y} years")
                                    ->modify("+{$date_restant->m} months")
                                    ->modify("+{$date_restant->d} days")
                                    ->modify("+{$date_restant->h} hours")
                                    ->modify("+{$date_restant->i} minutes")
                                    ->modify("+{$date_restant->s} seconds");

                                // Stocker la nouvelle date de fin dans le basket
                                $basket->setEndAt($date_limite);
                                $verifie = true;
                            }else{
                                $basket->setEndAt($date_limite);
                                $verifie = true;
                            }

                        }else{

                            //incrementation du bord
                            $allUser = $bord->getAllUser();
                            $allUser +=1;
                            $bord->setAllUser($allUser);

                            $basket = new UserBord();
                            $basket
                                ->setUser($user)
                                ->setVisible(1)
                                ->setBord($bord)
                                ->setRecordedAt(new \DateTimeImmutable())
                                ->setEndAt($date_limite);
                            $verifie = true;

                        }
                        //enregistrement des nouveau soldes et du nouveau nombre d'utilisateur
                        if($verifie == true){
                            $all_gain_libreur = filter_var($bord->getAllGainBord(), FILTER_VALIDATE_FLOAT);
                            $all_gain_infoox = filter_var($bord->getAllGainInfooxschool(), FILTER_VALIDATE_FLOAT);
                            $prix = filter_var($bord->getPrice(), FILTER_VALIDATE_FLOAT);
                            if(isset($achat_time)){
                                if($achat_time == '1 an'){
                                    $add_libreur =  $prix;
                                    $add_infoox = ceil($prix + ($prix/4)) - $add_libreur;
                                }
                                elseif($achat_time == '3 mois'){
                                    $add_libreur = $prix/4 + 70;
                                    $add_infoox = ceil(($prix + ($prix/4))/4 + 200) - $add_libreur;
                                }
                                elseif($achat_time == '1 mois'){
                                    $add_libreur = $prix/12 + 52.5;
                                    $add_infoox =  ceil((($prix + ($prix/4))/4 + 200)/3 + 150) - $add_libreur;
                                }
                                elseif($achat_time == '1 semaine'){
                                    $add_libreur = $prix/48 + 35;
                                    $add_infoox = ceil(((($prix + ($prix/4))/4 + 200)/3 + 150)/4 + 100) - $add_libreur;
                                }
                                elseif($achat_time == '1 jour' || $achat_time == '3 jours'){
                                    $add_libreur = $prix/336 + 17.5;
                                    $add_infoox = ceil((((($prix + ($prix/4))/4 + 200)/3 + 150)/4 + 100)/7 + 50) - $add_libreur;
                                }
                                $all_gain_libreur = $all_gain_libreur + $add_libreur;
                                $all_gain_infoox = $all_gain_infoox + $add_infoox;
                            }
                                $bord
                                    ->setAllGainInfooxschool($all_gain_infoox)
                                    ->setAllGainBord($all_gain_libreur);
                                $manager->flush();
                                return $this->redirectToRoute('app_basket');

                        }

                        return $this->redirect($request->headers->get('referer'));
                    }
                }
                else {
                    return $this->redirect($request->headers->get('referer'));
                }

            } catch (Exception $e) {
                echo "Erreur :" . $e->getMessage();
            }
        } else {
            return $this->redirect($request->headers->get('referer'));
        }











































    }
    public static function getCurrentUrl()
    {
        return  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";

    }
}

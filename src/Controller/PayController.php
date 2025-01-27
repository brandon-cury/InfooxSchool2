<?php

namespace App\Controller;



use App\Cinetpay\CinetPay;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public static function getCurrentUrl()
    {
        return  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";

    }
}

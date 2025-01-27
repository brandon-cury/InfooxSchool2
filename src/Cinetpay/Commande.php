<?php
namespace App\Cinetpay;

use PDO;
use PDOException;

class Commande
{
    public $transaction_id;
    public $status;
    public $id;
    
    /**
     * permet de savoir le mombre de fois qu'on n'est passe par le systeme de payement
     */
    public function increment($transaction_id){
        try{
            $pdo = new PDO("mysql:host=localhost;dbname=c1695521c_infoox;charset=utf8", 'c1695521c_moukam', 'Ma@670104245', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $commande_query = $pdo->query("SELECT * FROM commandes WHERE transaction_id = $transaction_id");
            $commande = $commande_query->fetch(PDO::FETCH_ASSOC);
            if($commande_query->rowCount() != 0){
                $increment = filter_var($commande['int'], FILTER_VALIDATE_INT);
                $increment = $increment + 1;

            } 
            $prepare = $pdo->prepare("UPDATE commandes SET `int`=? WHERE transaction_id = ?");
            $prepare->execute([
                $increment,
                $transaction_id,
            ]); 

        }catch(PDOException $e){
            dd("erreur: " . $e->getMessage());
        }
    }

    public function getCurrentUrl()
    {
       return  $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";
        
    }
    /**
     * creer une nouvelle colone de commande avec son identifiant
     */
    public function create($transaction_id)
    {
        try{
            $pdo = new PDO("mysql:host=localhost;dbname=c1695521c_infoox;charset=utf8", 'c1695521c_moukam', 'Ma@670104245', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $prepare = $pdo->prepare("INSERT INTO commandes (transaction_id) VALUES (:transaction_id)");
            $prepare->bindValue(':transaction_id', $transaction_id);
            $new_commende_id = $prepare->execute();

            if($new_commende_id){
                $id = $pdo->lastInsertId();
                $commande_query = $pdo->query("SELECT * FROM commandes WHERE transaction_id = $transaction_id AND id= $id");
                $commande = $commande_query->fetch(PDO::FETCH_ASSOC);
                if($commande_query->rowCount() != 0){
                    $this->transaction_id = $commande['transaction_id'];
                    $this->status = $commande['status'];
                    $this->id = $commande['id'];
                }

            }
        }
        catch(PDOException $e){
            dd("erreur: " . $e->getMessage());
        }
    }
    /**
     * Mise à jour d'une ligne spécifique statut 00 true
     */
    public function update(): bool
    {
        try{
            $pdo = new PDO("mysql:host=localhost;dbname=c1695521c_infoox;charset=utf8", 'c1695521c_moukam', 'Ma@670104245', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $prepare = $pdo->prepare("UPDATE commandes SET `status`=? WHERE transaction_id = ? AND id=?");
            $verifi_request = $prepare->execute([
                0,
                $this->transaction_id,
                $this->id
            ]);            
        }catch(PDOException $e){
            dd("erreur: " . $e->getMessage());
        }
        if($verifi_request){
            return true;
        }
        return false;
    }
    public function set_transactionId($transaction_id){
        try{
            $pdo = new PDO("mysql:host=localhost;dbname=c1695521c_infoox;charset=utf8", 'c1695521c_moukam', 'Ma@670104245', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $commande_query = $pdo->query("SELECT * FROM commandes WHERE transaction_id = $transaction_id");
            $commande = $commande_query->fetch(PDO::FETCH_ASSOC);
            if($commande_query->rowCount() != 0){
                $this->transaction_id = $commande['transaction_id'];
                $this->status = $commande['status'];
                $this->id = $commande['id'];
            }            
        }catch(PDOException $e){
            dd("erreur: " . $e->getMessage());
        }
    }



}
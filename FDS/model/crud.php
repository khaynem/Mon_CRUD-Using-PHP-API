<?php

interface CrudInterface{
    public function getAll();
    public function getOne();
    public function insert();
    public function update();
    public function delete();
}

class crud{

    protected $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function getAll(){
        $sql = "SELECT * FROM users";
        try{
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute()){
                $data =  $stmt->fetchAll();
                if ($stmt->rowCount() > 0){
                    return $data;
                }else{
                    http_response_code(404);
                    return 'NO EXISTING RECORDS FOUND';
                }
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    } 

    public function getOne($data){
        $sql = "SELECT * FROM users WHERE user_id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->user_id])){
                $data =  $stmt->fetchAll();
                if ($stmt->rowCount() > 0){
                    return $data;
                }else{
                    http_response_code(404);
                    return 'USER NOT FOUND';
                }
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function insert($data){
        $sql = 'INSERT INTO users(firstname, lastname, is_admin) VALUES(?, ?, DEFAULT)';

        if (!isset($data->firstname) || !isset($data->lastname)) {
            return "Error: firstname and lastname are required fields.";
        }

        if (empty($data->firstname) || empty($data->lastname)) {
            return "Error: firstname and lastname cannot be empty.";
        }

        try{
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->firstname, $data->lastname])){
                return "DATA IS SUCCESSFULLY INSERTED";
            }else{
                echo json_encode(["msg"=>"Data unsuccessfully inserted"]);
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function update($data){
        $sql = "UPDATE users SET is_admin = CASE WHEN is_admin = 0 THEN 1 WHEN is_admin = 1 THEN 0 END WHERE user_id = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->user_id])) {
                if ($stmt->rowCount() > 0){
                    echo json_encode(["message"=>"CHANGES SAVED"]);
                    return $this->getOne((object)['user_id' => $data->user_id]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message"=>"UNKNOWN USER"]);
                }
            }
        } catch (PDOException $e) {
            return $e->getMessage();  
        }
    } 

    public function delete($data){
        $sql = "DELETE FROM users WHERE user_id = ?";
    
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->user_id])) {
                if ($stmt->rowCount() > 0){
                echo json_encode(["message"=>"USER IS DELETED SUCCESSFULLY"]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message"=>"INVALID USER OR DELETED RECORD"]);
                }
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}
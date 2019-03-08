<?php
/*
 * DB Class
 * This class is used for database related (connect, insert, update, and delete) operations
 * @author    CodexWorld.com
 * @url        http://www.codexworld.comcgfcgfcgc
 * @license    http://www.codexworld.com/license
 */
class DB{
    private $dbHost     = "127.0.0.1";
    private $dbUsername = "root";
    private $dbPassword = "root1234";
    private $dbName     = "students";
    
    public function __construct(){
        if(!isset($this->db)){
            // Connect to the database
            $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
            if($conn->connect_error){
                die("Failed to connect with MySQL: " . $conn->connect_error);
            }else{
                $this->db = $conn;
            }
        }
    }
    
    /*
     * Returns rows from the database based on the conditions
     * @param string name of the table
     * @param array select, where, order_by, limit and return_type conditions
     */
    public function getRows($table,$conditions = array()){
        $sql = 'SELECT ';
        $sql .= array_key_exists("select",$conditions)?$conditions['select']:'*';
        $sql .= ' FROM '.$table;
        if(array_key_exists("where",$conditions)){
            $sql .= ' WHERE ';
            $i = 0;
            foreach($conditions['where'] as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $sql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }
        
        if(array_key_exists("order_by",$conditions)){
            $sql .= ' ORDER BY '.$conditions['order_by']; 
        }
        
        if(array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit']; 
        }elseif(!array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['limit']; 
        }
        
        $result = $this->db->query($sql);
        
        if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){
            switch($conditions['return_type']){
                case 'count':
                    $data = $result->num_rows;
                    break;
                case 'single':
                    $data = $result->fetch_assoc();
                    break;
                default:
                    $data = '';
            }
        }else{
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $data[] = $row;
                }
            }
        }
        return !empty($data)?$data:false;
    }
    
    
    public function update($table, $data, $conditions){
        $m = $conditions['MISID'];
        $fn = $data['FullName'];
        $yoi = $data['YearOfAdmission'];
        $f = $data['Fees'];
        $c = $data['CGPA'];
        //$query = "UPDATE ".$table." SET  FullName = ".$FullName.", YearOfAdmission = ".$YearOfAdmission.", Fees = ".$Fees.", CGPA = ".$CGPA." WHERE MISID = ".$MISID.";";
        $query = "UPDATE ".$table." SET  FullName = '".$fn."', YearOfAdmission = '".$yoi."', Fees = '".$f."', CGPA = '".$c."' WHERE MISID = '".$m."';";
        $update = $this->db->query($query);
        //return $update?true:false;
        return $update?$this->db->affected_rows:false;

    }
    public function insert($table, $data){
        $m = $data['MISID'];
        $fn = $data['FullName'];
        $yoi = $data['YearOfAdmission'];
        $f = $data['Fees'];
        $c = $data['CGPA'];
        //$query = "UPDATE ".$table." SET  FullName = ".$FullName.", YearOfAdmission = ".$YearOfAdmission.", Fees = ".$Fees.", CGPA = ".$CGPA." WHERE MISID = ".$MISID.";";
        //$query = "UPDATE ".$table." SET  FullName = '".$fn."', YearOfAdmission = '".$yoi."', Fees = '".$f."', CGPA = '".$c."' WHERE MISID = '".$m."';";
        $query = "INSERT INTO ".$table." VALUES ('".$m."', '".$fn."', '".$yoi."', '".$f."', '".$c."');";
        $update = $this->db->query($query);
        //return $update?true:false;
        return $update?$this->db->affected_rows:false;

    }
    /*
     * Delete data from the database
     * @param string name of the table
     * @param array where condition on deleting data
     */
    public function delete($table,$conditions){
        $whereSql = '';
        if(!empty($conditions)&& is_array($conditions)){
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach($conditions as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $whereSql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }
        $query = "DELETE FROM ".$table.$whereSql;
        $delete = $this->db->query($query);
        return $delete?true:false;
    }
}


<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    // variables to work
    require_once("../requires/pgconnection.php");
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    $newpath = explode("viewgram//", $data["path"])[1]; // like split function in js
    $allowedtypes = ["jpeg", "png", "mp4", "jpg"];
    // end of variables to work
    
//    $mydata = array($data["userid"], $newpath, $data["caption"], date('Y-m-d'), $data["lat"], $data["long"], 0);
        $mydata = array($data["userid"], $newpath, $data["caption"], date('Y-m-d H:i:s'), $data["lat"], $data["long"],$data["location"], 0);

    //echo json_encode($mydata);

    $connection = getConnection();
    if(!($data["path"] === '')) {
            try {
                if($connection) {
                    $results = pg_query_params($connection, "INSERT INTO post_1 (id_user, post_filename, post_caption, created_at, post_latitude, post_longitude,location,report_count ) VALUES ($1, $2, $3, $4, $5, $6, $7,$8) RETURNING post_id", $mydata);
                    $r = pg_fetch_assoc($results);
                    $t;
                    if($r){ 
                        for ($i = 0; $i < count($data["tagged"]); $i++) {
                            $tagged = pg_query_params($connection, "SELECT id_user FROM app_user WHERE username = $1", array(trim($data["tagged"][$i])));
                            $t = pg_fetch_assoc($tagged);
                            pg_query_params($connection, "INSERT INTO tagged_user (post_id, id_user) VALUES ($1, $2)", array($r["post_id"], $t["id_user"]));
                        }
                        for ($j = 0; $j < count($data["ht"]); $j++) {
                            $htexists = pg_query_params($connection, "SELECT * FROM hashtags WHERE hashtag = $1", array(trim($data["ht"][$j])));
                            $hte = pg_fetch_assoc($htexists);
                            // echo json_encode($hte["hashtag_id"]);
                            if($hte) {
                                pg_query_params($connection, "INSERT INTO post_hashtag (post_id, hashtag_id) VALUES ($1, $2)", array($r["post_id"], $hte["hashtag_id"]));      
                            } else {
                                $ht = pg_query_params($connection, "INSERT INTO hashtags (hashtag) VALUES ($1) RETURNING hashtag_id", array(trim($data["ht"][$j])));
                                $h = pg_fetch_assoc($ht);
                                pg_query_params($connection, "INSERT INTO post_hashtag (post_id, hashtag_id) VALUES ($1, $2)", array($r["post_id"], $h["hashtag_id"]));
                            }
                        }  
                        echo json_encode([
                            "status" => 200,
                            "msg" => "File uploaded successfully!"
                        ]);
                    } else {
                        echo json_encode([
                            "status" => 404,
                            "msg" => "Error en insert"
                        ]);
                    }
                } else {
                    json_encode([
                        "status" => 404,
                        "msg" => "Error while connection to database..."
                    ]);
                }
            } catch (Exception $e) {
                json_encode([
                    "status" => 404,
                    "msg" => "Exception... -> " . $e->getMessage()
                ]);
            }
    } else {
        echo json_encode([
            "status" => 404,
            "msg" => "YOU HAVE TO SELECT A PICTURE!"
        ]);        
    }


?>
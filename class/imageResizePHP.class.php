<?php 
/**
    *
    * @package ImageResizePHP
    * @author Original Author Matheus Palma mmatheuspalma@gmail.com
    * @copyright 2016 Matheus Palma
    * @since File avaible since release alfa 1.0
    * @version alfa 1.0
    *
 */

class ImageResizePHP {

    public function resize_image($file, $w, $h, $ext) {
       list($width, $height) = getimagesize($file);
       if($ext == 'jpeg'){
          $src = imagecreatefromjpeg($file);
       }elseif($ext == 'gif'){
          $src = imagecreatefromgif($file);
       }elseif($ext == 'png'){
          $src = imagecreatefrompng($file);
       }
       $dst = imagecreatetruecolor($w, $h);
       imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
       return $dst;
    }

    public function formatSizeUnits($bytes){
        if ($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576){
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024){
            $bytes = number_format($bytes / 1024, 2) . ' kB';
        }
        elseif ($bytes > 1){
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1){
            $bytes = $bytes . ' byte';
        }
        else{
            $bytes = '0 bytes';
        }
        return $bytes;
    }

    public function compress_image($source_url, $destination_url, $quality, $folder,$msg) {
        $info = getimagesize($source_url); 
        $old_size = filesize($source_url); 

        if($info['mime'] == 'image/jpeg'){
            $image = imagecreatefromjpeg($source_url);
            $ext = 'jpeg';
        }elseif ($info['mime'] == 'image/gif'){
            $image = imagecreatefromgif($source_url);
            $ext = 'gif';
        }elseif ($info['mime'] == 'image/png'){
            $image = imagecreatefrompng($source_url);
            $ext = 'png';
        }
            
        if($ext == 'jpeg'){
            imagejpeg($image, $folder.$destination_url, $quality); 
        }elseif($ext == 'gif'){
            imagegif($image, $folder.$destination_url, $quality); 
        }elseif($ext == 'png'){
            $quality = $quality / 10 - 1;
            imagepng($image, $folder.$destination_url, $quality); 
        }      
        
        list($width, $height) = getimagesize($source_url);

        if($msg == 1){
            echo $width."\n*\n";
            echo $height;
            echo "</br>";
            echo "Archive :\n".$destination_url."</br>";
        }


        if($width == $height){
            if($width >= 720){
                $w = 720;
                $h = 720;
            }elseif($width < 720){
                $w = $width;
                $h = $height;
            }
        }else {      
            if($width >= 1280){
                $h = $height / $width * 1280;
                $w = $width / $height * $h;
            }elseif($width < 1280){
                $w = $width;
                $h = $height;
            }  
        }

        $img = $this-> resize_image($folder.$destination_url, $w, $h, $ext);

        if($ext == 'jpeg'){
            imagejpeg($img, $folder.$destination_url, $quality); 
        }elseif($ext == 'gif'){
            imagegif($img, $folder.$destination_url, $quality); 
        }elseif($ext == 'png'){
            imagepng($img, $folder.$destination_url, $quality); 
        }  

        if($msg == 1){
            $new_size = filesize($folder.$destination_url);
            $reduce = ($new_size - $old_size) * -1;
            $reduce_percent = ($old_size * 100) / $new_size;

            echo "Old size :\n".$this->formatSizeUnits($old_size);
            echo "</br>";
            echo "New size :\n".$this->formatSizeUnits($new_size);
            echo "</br>";
            echo "Reduce :\n".$this->formatSizeUnits($reduce)."(".$reduce_percent."%)";
        }

        return $destination_url;
    }

    public function insertImage($image,$table,$fields,$values){
        global $conex;

        foreach ($fields as $key => $field) {
            @$allfields .= $field.",";            
        }

        foreach ($values as $key => $value) {
            @$allvalues .= "'".$value."',";
        }

        $allfields = substr($allfields,0,-1);
        $allvalues = substr($allvalues,0,-1);

        $in = $conex -> query("INSERT INTO $table ($allfields) VALUES ($allvalues)");
        if($in == true){
            echo "Image insert with success !";
        }else{
            echo "Error in the image insertion !";
            printf("%s\n", $conex->error);
        }
        return $in;
    }   

    public function updateImage($image,$table,$fields,$values,$table_id,$id){
        global $conex;

        $count = count($fields);
        for($i = 0;$i < $count;$i++){
            @$allfields .= $fields[$i]."\n=\n'".$values[$i]."',";
        }
        $allfields = substr($allfields, 0,-1);
    
        $up = $conex -> query("UPDATE $table SET $allfields WHERE $table_id = $id");
        if($up == true){
            echo "Image update with success !";
        }else{
            echo "Error in the image update !";
            printf("%s\n", $conex->error);
        }
        return $up;
    }   

    public function deleteImage($table,$id){
        global $conex;

        $del = $conex -> query("DELETE * FROM $table WHERE id = $id");
        if($del == true){
            echo "Image delete with success !";
        }else{
            echo "Error delete image !";
            printf("%s\n", $conex->error);
        }
        return $del;
    }   
}

?>
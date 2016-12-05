<?php
/**
    * @package falconPHP
    * @author Original Author Matheus Palma mmatheuspalma@gmail.com
    * @copyright 2016 Matheus Palma
    * @since File avaible since release alfa 1.0
    * @version 2.2
    *
 */
	class falcon{
		// Connection
		private $conn;

		// ImageResize
		private $quality;
		private $folder;
		private $tableImg;
		private $fieldImg;

		// Items
		private $item;
		private $table;

		public function conn($host,$dbname,$user,$pass){
			$this->conn = new PDO("mysql:host=".$host.";dbname=".$dbname."","".$user."","".$pass."",array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
			return $this->conn;
		}

		public function config($item,$table){
			$this->item = $item;
			$this->table = $table;
		}

		// ImageResize
		public function imagesConfig($folder,$tableImg,$fieldImg,$quality){
			$this->folder = $folder;
			$this->tableImg = $tableImg;
			$this->fieldImg = $fieldImg;
			$this->quality = $quality;
		}

	    private function resizeImage($file, $w, $h, $ext) {
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

	    private function compressImage($source_url,$destination_url) {
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
	            imagejpeg($image, $this->folder.$destination_url, $this->quality);
	        }elseif($ext == 'gif'){
	            imagegif($image, $this->folder.$destination_url, $this->quality);
	        }elseif($ext == 'png'){
	            $this->quality = $this->quality / 10 - 1;
	            imagepng($image, $this->folder.$destination_url, $this->quality);
	        }

	        list($width, $height) = getimagesize($source_url);

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
	        $img = $this-> resizeImage($this->folder.$destination_url, $w, $h, $ext);

	        if($ext == 'jpeg'){
	            imagejpeg($img, $this->folder.$destination_url, $this->quality);
	        }elseif($ext == 'gif'){
	            imagegif($img, $this->folder.$destination_url, $this->quality);
	        }elseif($ext == 'png'){
	            imagepng($img, $this->folder.$destination_url, $this->quality);
	        }
	        return $destination_url;
	    }
	    //

		private function criterion($criterion_t,$criterion_c){
			if(is_array($criterion_t) && is_array($criterion_c)){
				$count = count($criterion_t);
		        for($i = 0;$i < $count;$i++){
		            @$criterion .= $criterion_t[$i]."\n=\n'".$criterion_c[$i]."'\nAND\n";
		        }
		        $criterion = substr($criterion, 0,-4);
			}else{
				$criterion = $criterion_t."\n=\n'".$criterion_c."'";
			}
			return $criterion;
		}

		private function fieldsObj(){
			$rs = $this->conn->prepare("SELECT * FROM ".$this->table."");
			$rs->execute();

			for ($i = 0; $i < $rs->columnCount(); $i++) {
			    $col = $rs->getColumnMeta($i);
			    $fields[] = $col['name'];
			}
			return $fields;
		}

		private function fields($table){
			$rs = $this->conn->prepare("SELECT * FROM ".$table."");
			$rs->execute();

			for ($i = 0; $i < $rs->columnCount(); $i++) {
			    $col = $rs->getColumnMeta($i);
			    $fields[] = $col['name'];
			}

			if(is_array($fields)){
				foreach($fields as $field){
					@$fields_ .= $field.",";
				}
				$fields = substr($fields_,0, -1);
			}else {
				$fields = $fields;
			}
			return $fields;
		}

		private function values($values){
			if(is_array($values)){
				foreach($values as $value){
					if(is_array($value)){
						@$values_ .= "'".serialize($value)."',";
					}else {
						@$values_ .= "'".$value."',";
					}
				}
				$values = substr($values_,0,-1);
			}else {
				$values = "'".$values."'";
			}
			return $values;
		}

		private function values2($values){
            $rs = $this->conn->prepare("SELECT * FROM ".$this->table."");
            $rs->execute();

            for($i = 0; $i < $rs->columnCount(); $i++) {
                $col = $rs->getColumnMeta($i);
                $fields[] = $col['name'];
            }

            foreach($fields as $key => $field){
              $fields_[$field] = $field;
            }

	        foreach($fields_ as $key => $field){
	          if(is_array($values[$field])){
	            $value = "'".serialize($values[$field])."',";
	            $result .= $value;
	          }else {
	            $value = "'".$values[$field]."',";
	            $result .= $value;
	          }
	        }
	        return substr($result,0, -1);
		}

		private function fieldsValues($values){
			$rs = $this->conn->prepare("SELECT * FROM ".$this->table."");
			$rs->execute();

			for ($i = 0; $i < $rs->columnCount(); $i++) {
			    $col = $rs->getColumnMeta($i);
			    $fields[] = $col['name'];
			}

			if(is_array($fields) && is_array($values)){
				$count = count($fields);
		        for($i = 0;$i < $count;$i++){
		            @$fieldsvalues .= $fields[$i]."\n=\n'".$values[$i]."',";
		        }
		        $fieldsvalues = substr($fieldsvalues, 0,-1);
			}else{
				$fieldsvalues = $fields."\n=\n'".$values."'";
			}
			return $fieldsvalues;
		}

		public function fieldsValues2($values){
            $rs = $this->conn->prepare("SELECT * FROM ".$this->table."");
            $rs->execute();

            for($i = 0; $i < $rs->columnCount(); $i++) {
                $col = $rs->getColumnMeta($i);
                $fields[] = $col['name'];
            }

            foreach($fields as $key => $field){
              $fields_[$field] = $field;
            }

			foreach($fields_ as $key => $field){
              if(is_array($values[$field])){
                $fields = $field."\n='".serialize($values[$field])."'";
                @$result .= $fields.",";
              }else {
                $fields = $field."\n='".$values[$field]."'";
                @$result .= $fields.",";
              }
            }
            return substr($result,0, -1);
		}

		public function add($table,$values,$msg){
			$fields = $this->fields($table);
			$values = $this->values($values);

			$in = $this->conn->prepare("INSERT INTO $table ($fields) VALUES ($values)");
			$in->execute();

			if($msg == 1){
				if($in == true){
					echo "<script>alert('".$this->item." inserido(a) com sucesso');</script>";
		        }else{
					echo "Erro ao inserir o(a) ".$this->item."";
		        }
			}
			return $in;
		}

		private function delPath($path,$name){
			array_map("unlink", glob($path.$name));
		}

		private function up($criterion_t,$criterion_c,$values,$redirect){
			$criterion = $this->criterion($criterion_t,$criterion_c);
			$fieldsvalues = $this->fieldsvalues($this->table,$values);

	        $up = $this->conn-> prepare("UPDATE $this->table SET $fieldsvalues WHERE $criterion");
	        $up->execute();

	        if($up == true){
				echo "<script>alert('".$this->item." atualizado(a) com sucesso');</script>";
				if($redirect != 0){
					echo "<script>window.location.assign('".$redirect."');</script>";
				}
	        }else{
				echo "Erro ao atualizar o(a) ".$this->item."";
				if($redirect != 0){
					echo "<script>window.location.assign('".$redirect."');</script>";
				}
	        }
       		return $up;
		}

		public function del($criterion_t,$criterion_c,$redirect){
			$criterion = $this->criterion($criterion_t,$criterion_c);

			$del = $this->conn->prepare("DELETE FROM $this->table WHERE $criterion");
			$del->execute();

			if($del == true){
				echo "<script>alert('".$this->item." deletado(a) com sucesso');</script>";
				if($redirect != 0){
					echo "<script>window.location.assign('".$redirect."');</script>";
				}
	        }else{
				echo "Erro ao deletar o(a) ".$this->item."";
				if($redirect != 0){
					echo "<script>window.location.assign('".$redirect."');</script>";
				}
	        }
       		return $del;
		}

		// Item controller
		public function addItem($images,$values,$thumb){
			$fields = $this->fields($this->table);
			$values = $this->values2($values);

			$in = $this->conn->prepare("INSERT INTO $this->table ($fields) VALUES ($values)");
			$in->execute();
			$id = $this->conn->lastInsertId();

			// Thumb
			if($thumb > 0){
				$this->upThumb('id',$id,$thumb);
			}

			// Gallery
			$this->addImg($id,$images);

			if($in == true){
				echo "<script>alert('".$this->item." inserido(a) com sucesso');</script>";
	        }else{
				echo "Erro ao inserir o(a) ".$this->item."";
	        }
       		return $in;
		}

		public function addImg($id,$images){
			$numImg = count(array_filter($images['name']));

			for($i = 0;$i < $numImg;$i++){
				$name = $images['name'][$i];
				$source = $images['tmp_name'][$i];

				$image = $this->compressImage($source,$name);
				$this->add($this->tableImg,array('',$image,$id),0);
			}
		}

		public function addVitrine($images){
			$numImg = count(array_filter($images['name']));

			for($i = 0;$i < $numImg;$i++){
				$name = $images['name'][$i];
				$source = $images['tmp_name'][$i];

				$image = $this->compressImage($source,$name);
				$this->add($this->table,array('','',$image),1);
			}
		}

		public function upThumb($criterion_t,$criterion_c,$images){
			$criterion = $this->criterion($criterion_t,$criterion_c);

			$name = $images['name'];
			$source = $images['tmp_name'];

			$image = $this->compressImage($source,$name);
			$upImg = $this->conn->prepare("UPDATE $this->table SET $this->fieldImg = '$image' WHERE $criterion");
			$upImg->execute();

	        if($upImg == true){
				echo "<script>alert('Foto atualizado(a) com sucesso');</script>";
	        }else{
				echo "<script>alert('Erro ao atualizar o(a) Foto');</script>";
	        }
		}

		public function upImg($criterion_t,$criterion_c,$images){
			$criterion = $this->criterion($criterion_t,$criterion_c);
			$numImg = count(array_filter($images['name']));

			for($i = 0;$i < $numImg;$i++){
				$name = $images['name'][$i];
				$source = $images['tmp_name'][$i];

				$image = $this->compressImage($source,$name);
				$upImg = $this->conn-> prepare("UPDATE $this->tableImg SET $this->fieldImg = $name WHERE $criterion");
				$upImg->execute();

		        if($upImg == true){
					echo "<script>alert('Foto atualizado(a) com sucesso');</script>";
		        }else{
					echo "<script>alert('Erro ao atualizar o(a) Foto');</script>";
		        }
			}
		}

		public function upItem($criterion,$id,$images,$values,$thumb){
			$fieldsValues = $this->fieldsValues2($values);

			$up = $this->conn->prepare("UPDATE $this->table SET $fieldsValues WHERE $criterion = '$id'");
			$up->execute();

			// Thumb
			if($thumb > 0){
				$this->upThumb('id',$id,$thumb);
			}

			// Gallery
			$this->addImg($id,$images);

			if($up == true){
				echo "<script>alert('".$this->item." atualizado(a) com sucesso');</script>";
	        }else{
				echo "Erro ao atualizar o(a) ".$this->item."";
	        }
       		return $up;
		}

		public function upPic($files){
			$pic = $this->compressImage($files['tmp_name'],$files['name']);
			return $pic;
		}

		public function delImg($name,$criterion_t,$criterion_c,$redirect){
			$this->del($this->item,$this->table,$criterion_t,$criterion_c,$redirect);
			$this->delPath($this->folder,$name);

		}

		public function get($criterion_t,$criterion_c){
			$criterion = $this->criterion($criterion_t,$criterion_c);
			$fields = $this->fields($this->table);
			$fieldsObj = $this->fieldsObj($this->table);

			$list = $this->conn-> prepare("SELECT $fields FROM $this->table WHERE $criterion");
			$list->execute();

			foreach($list->fetchAll(PDO::FETCH_ASSOC) as $row){

			}

			foreach($fieldsObj as $key => $obj){
				$lista[$key] = $row[$obj];
				$lista[$obj] = $lista[$key];
			}

			return $lista;
		}

	}
?>
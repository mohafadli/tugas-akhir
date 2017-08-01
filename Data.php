<?php
	//cariRute();
	switch($_POST['action']){
		case 'createNode': createNode($_POST['id'],$_POST['info'],$_POST['status'],$_POST['x'],$_POST['y'],$_POST['jenis'],$_POST['price']); break;
		case 'createPath': createPath($_POST['node1'],$_POST['node2'],$_POST['const_perkm']); break;
		case 'deletePath': deletePath($_POST['id']); break;
		case 'getPath': getPath($_POST['id']); break;
		case 'getData': getData(); break;
		case 'cariRute': cariRute(); break;
		default: echo json_encode('failed'); break;
	}
	
	function createNode($id,$info,$status,$x,$y,$jenis){
		$myfile = fopen("object.txt", "r") or die("Unable to open file!");
		$text = fread($myfile,filesize("object.txt"));
		$object = json_decode($text,true);
		fclose($myfile);
		$object['nodes'][] = array('id' => $id,'x' => $x,'y' => $y, 'info' => $info, 'status' => $status,'jenis' => $jenis);
		$myfile = fopen("object.txt","w");
		fwrite($myfile,json_encode($object,JSON_PRETTY_PRINT));
		fclose($myfile);
		echo json_encode('success');
	}
	
	function createPath($node1,$node2,$rute,$cons_perkm){
		//koding mysql
		$myfile = fopen("object.txt", "r") or die("Unable to open file!");
		$text = fread($myfile,filesize("object.txt"));
		$object = json_decode($text,true);
		fclose($myfile);
		$distance = countDistance($object['nodes'][$node1],$object['nodes'][$node2]);
		//$price = number_format($distance*$cons_perkm,'.','');
		$price = number_format($distance*$cons_perkm,1,'.','');
		$object['paths'][] = array('id' => 'id_'.$node1.'-id_'.$node2,'fnode' => $node1, 'tnode' => $node2, 'distance' => $distance,'price' => $price);		
		$myfile = fopen("object.txt","w");
		fwrite($myfile,json_encode($object,JSON_PRETTY_PRINT));
		fclose($myfile);
		echo json_encode('success');
	}
	
	function deletePath($id){
		$myfile = fopen("object.txt", "r") or die("Unable to open file!");
		$text = fread($myfile,filesize("object.txt"));
		$object = json_decode($text,true);
		fclose($myfile);
		unset($object['paths'][$id]);
		$object['paths'] = array_values($object['paths']);
		$myfile = fopen("object.txt","w");
		fwrite($myfile,json_encode($object,JSON_PRETTY_PRINT));
		fclose($myfile); 
		echo json_encode('success');
	}
	
	function getPath($id){
		$myfile = fopen("object.txt", "r") or die("Unable to open file!");
		$text = fread($myfile,filesize("object.txt"));
		$object = json_decode($text,true);
		fclose($myfile);
		for($i=0; $i<count($object['paths']); $i++){
			if($object['paths'][$i]['id'] == $id){
				$id = $i;
				break;
			}
		}
		$idf = $object['paths'][$id]['fnode'];
		$idt = $object['paths'][$id]['tnode'];
		$nodef = $object['nodes'][$idf]['info'];
		$nodet = $object['nodes'][$idt]['info'];
		$distance = $object['paths'][$id]['distance'];
		$arr = array('id' => $id,'fnode' => $nodef, 'tnode' => $nodet, 'distance' => $distance);
		echo json_encode($arr);
	}
	
	function getData(){
		$myfile = fopen("object.txt", "r") or die("Unable to open file!");
		$text = fread($myfile,filesize("object.txt"));
		$object = json_decode($text,true);
		fclose($myfile);
		echo json_encode($object);
	}
	
	function countDistance($node1,$node2){
		$result = 0;
		$delta_lat = abs($node1['x'] - $node2['x']);
		$delta_lon = abs($node1['y'] - $node2['y']);
		//rumus haversine
		$result = sqrt(($delta_lat*$delta_lat)+($delta_lon*$delta_lon))*111.319;
		//return number_format($result);
		return number_format($result,1);
	}
		
	function cariRute(){
		include_once('Route.php');
		$myfile = fopen("object.txt", "r") or die("Unable to open file!");
		$text = fread ($myfile,filesize("object.txt"));
		$object = json_decode($text,true);
		fclose($myfile);
		$id = $_POST['id'];
		//$id = "42";
		$obj = []; 
		$shortest_obj = [];
		for($i=0; $i<count($object['nodes']); $i++){
			//hanya memasukan node yang berstatus objek dan tidak sama dengan id
			//if($object['nodes'][$i]['status'] == 'object' && $i != $id){
			if($object['nodes'][$i]['status'] == 'object'){
				array_push($obj,$object['nodes'][$i]);
			}
		}
		/*
		echo "<br/> data yang di push ";
		echo "<br/>";
		echo "<br/>".print_r($obj);
		echo "<br/>";
		*/
		for($x=0; $x<count($obj); $x++){
			$routes = [];
			$nodes = new Route();
			$check = true;
			$ind = 0;
			$id = $_POST['id'];
			//$id = "42";
			//echo "<br/> Inisial : ".$id;
			//echo "<br/>";
			for($i=0; $i<count($object['paths']); $i++){
				$nodes = new Route();
				$nodes->add_node($object['nodes'][$id]);
				if(($object['paths'][$i]['fnode'] == $id) && $nodes->find_node($object['paths'][$i]['tnode'],$id)){
					$nodes->add_node($object['nodes'][$object['paths'][$i]['tnode']]);
					$nodes->set_price($object['paths'][$i]['price']);
					$nodes->set_distance($object['paths'][$i]['distance']);
					$routes[$ind] = $nodes;	
					/*			
					echo "<br/> Open node : ".$object['nodes'][$object['paths'][$i]['tnode']]['id'];
					echo "<br/>";
					echo "<br/> price : ".$object['paths'][$i]['price'];
					echo "<br/>";
					echo "<br/> distance : ".$object['paths'][$i]['distance'];
					echo "<br/>";
					echo "<br/> ".print_r($nodes);
					echo "<br/>";				
					echo "<br/> ".print_r($ind);
					*/
					$ind++;					
				}else if(($object['paths'][$i]['tnode'] == $id) && $nodes->find_node($object['paths'][$i]['fnode'],$id)){
					$nodes->add_node($object['nodes'][$object['paths'][$i]['fnode']]);					
					$nodes->set_price($object['paths'][$i]['price']);
					$nodes->set_distance($object['paths'][$i]['distance']);
					$routes[$ind] = $nodes;				
					/*
					echo "<br/> Open node : ".$object['nodes'][$object['paths'][$i]['fnode']]['id'];
					echo "<br/>";
					echo "<br/> price : ".$object['paths'][$i]['price'];
					echo "<br/>";
					echo "<br/> distance : ".$object['paths'][$i]['distance'];
					echo "<br/>";
					echo "<br/> ".print_r($nodes);				
					echo "<br/>";
					echo "<br/> ".print_r($ind);
					*/
					$ind++;					
					//echo "<br/>";
				}
			}	
			//echo "<br/> hasilnya";		
			//echo "<br/>";
			$routes = mergeSort($routes,$obj[$x]); //obj	
			//echo "<br/>".print_r($routes);
			//echo "<br/>";			
			$ind--;
			$data = serialize($routes[$ind]);
			unset($routes[$ind]);
			$check = true;
			while($check){
			//	echo "Inisial : ".$id;
				for($i=0; $i<count($object['paths']); $i++){
					$nodes = unserialize($data);
					$id = $nodes->get_last_node()['id'];
					if(($object['paths'][$i]['fnode'] == $id) && $nodes->find_node($object['paths'][$i]['tnode'],$id)){
						$nodes->add_node($object['nodes'][$object['paths'][$i]['tnode']]);
						$nodes->set_price($object['paths'][$i]['price']);
						$routes[$ind] = $nodes;						
						/*
							echo "<br/> Open node : ".$object['nodes'][$object['paths'][$i]['fnode']]['id'];
							echo "<br/>";
							echo "<br/> price : ".$object['paths'][$i]['price'];
							echo "<br/>";
							echo "<br/> ".print_r($nodes);						
							*/
						$ind++;
					}else if(($object['paths'][$i]['tnode'] == $id) && $nodes->find_node($object['paths'][$i]['fnode'],$id)){
						$nodes->add_node($object['nodes'][$object['paths'][$i]['fnode']]);						
						$nodes->set_price($object['paths'][$i]['price']);
						$routes[$ind] = $nodes;						
						/*
							echo "<br/> Open node : ".$object['nodes'][$object['paths'][$i]['fnode']]['id'];
							echo "<br/>";
							echo "<br/> price : ".$object['paths'][$i]['price'];
							echo "<br/>";
							echo "<br/> ".print_r($nodes);						
							*/
						$ind++;
					}
				}				
				$routes = mergeSort($routes,$obj[$x]);
				if($routes[count($routes)-1]->get_last_node()['info'] == $obj[$x]['info']){
				$check = false;
				}else{
					$ind--;
					$data = serialize($routes[$ind]);
					unset($routes[$ind]);
				}				
			}
			$shortest_obj[$x] = $routes[$ind-1];				
		}
			echo json_encode(findMin($shortest_obj));	
			//return print_r($nodes);									
	}
	function findMin($arr){
		$index = 0;
		//for($i=1; $i<count($arr); $i++){
		for($i=0; $i<count($arr); $i++){
			if($arr[$i]->get_price() < $arr[$index]->get_price()){
				$index = $i;
			}
		}
		return $arr[$index];
	}
		
	function mergeSort($array,$node){
		if(count($array) == 1){
			return $array;
		}
		$mid = count($array) / 2;
		$left = array_slice($array, 0, $mid);
		$right = array_slice($array, $mid);
		$left = mergeSort($left,$node);
		$right = mergeSort($right,$node);
		return merge($left, $right, $node);
	}

	function merge($left, $right, $node){
		$res = array();
		while (count($left) > 0 && count($right) > 0){
			if(($left[0]->get_price()+$left[0]->get_heuristic($node)) < ($right[0]->get_price()+$right[0]->get_price($node))){
				$res[] = $right[0];
				$right = array_slice($right , 1);
			}else{
				$res[] = $left[0];
				$left = array_slice($left, 1);
			}
		}
		while (count($left) > 0){
			$res[] = $left[0];
			$left = array_slice($left, 1);
		}
		while (count($right) > 0){
			$res[] = $right[0];
			$right = array_slice($right, 1);
		}
		return $res;
	}
?>
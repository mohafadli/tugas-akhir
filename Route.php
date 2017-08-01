<?php
	

	 class Route{
		 
		 var $node;
		 var $index;
		 var $distance;
		 var $price;
		 
		 function __construct(){
			 $this->node = [];
			 $this->index = 0;
			 $this->distance = 0;
			 $this->price = 0;
		 }
		 
		 function add_node($new_node){
			 $this->node[$this->index] = $new_node;
			 $this->index++;
		 }
		 
		 function get_node($i){
			 return $this->node[$i];
		 }
		 
		 function get_last_node(){
			 return $this->get_node($this->index-1);
		 }
		 
		 function find_node($id1,$id2){
			 $out = true;
			 for($i=0; $i<count($this->node); $i++){
				if($this->node[$i]['id'] == $id1 && $id1 != $id2){
					$out = false;
					break;
				}
			}
			return $out;
		 }
		 
		 function set_distance($dist){
			$this->distance = $dist + $this->distance; 
		 }
		 
		 function get_distance(){
			return $this->distance;
		 }
		 
		 function get_heuristic($node1){
			 $node2 = $this->get_last_node();
			 //return sqrt(pow(($node1['x']-$node2['x']),2)+pow(($node1['y']-$node2['y']),2))*111.319;
			 return sqrt(pow(($node1['x']-$node2['x']),2)+pow(($node1['y']-$node2['y']),2));
		 }
		 
		 function set_price($price){
			 $this->price = $this->price + $price;
		 }
		 
		 function get_price(){
			 return $this->price;
		 }		 		
	}
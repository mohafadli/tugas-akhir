<!DOCTYPE html>
<html>
	<head>
		<title>Telusur</title>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCtIECE9QLP6te8p6GyfjQFXZTMaHmFBS0"></script>
		<script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
		<script type="text/javascript">
			$(function() {
				$('#form_path').hide();
			});
			//mhy-ledeng
			var const_perkm = 250;
			var object;
			refreshObject();
			var map;
			var sumbu_x = -6.946953;
			var sumbu_y = 107.662624;
			var markers = [];
			var myCenter = new google.maps.LatLng(sumbu_x,sumbu_y);			
			function triggerBtn(){
				if($('#id_btn').html() == "atur jalur"){
					document.getElementById('id_btn').innerHTML = "tutup";
					$('#form_path').show();
				}else{
					document.getElementById('id_btn').innerHTML = "atur jalur";
					$('#form_path').hide();
				}
			}
			
			var markerIndexNode = 0;
			var markerIndexObject = 0;

			function viewAllNode(){
				if($('#btnViewAllNode').html() == "tampil node"){
					document.getElementById('btnViewAllNode').innerHTML = "tutup node";
					var infoWindow;
					for(var i=0; i<object.nodes.length; i++){
						var info = object.nodes[i].info;
						var x = object.nodes[i].x;
						var y = object.nodes[i].y;
						markers[i] = new google.maps.Marker({
										position: new google.maps.LatLng(x, y),
										html:'<h3>ID: id_'+i+'</h3><h3>'+info+'</h3>',
										map: map
									});
						markers[i].addListener("click", function() {
										new google.maps.InfoWindow({
											content: this.html
										}).open(map,this);
						});
					}
				}else{
					document.getElementById('btnViewAllNode').innerHTML = "tampil node";
					for(var i=0; i<markers.length; i++){
						markers[i].setMap(null);
					}
				}
			}
			
			function viewObject(){
				if($('#btnViewIcon').html() == "tampil objek"){
					if(markerIndexNode != 0){
						markerIndexObject = markerIndexNode;
					}
					document.getElementById('btnViewIcon').innerHTML = "tutup objek";
					for(var i=0; i<object.nodes.length; i++){
						if(object.nodes[i].status == "object"){
							var info = object.nodes[i].info;
							var x = object.nodes[i].x;
							var y = object.nodes[i].y;
							markers[markerIndexObject] = new google.maps.Marker({
											position: new google.maps.LatLng(x, y),
											html:'<h3>ID: id_'+i+'</h3><h3>'+info+'</h3>',
											map: map
										});
							markers[markerIndexObject].addListener("click", function() {
										new google.maps.InfoWindow({
											content: this.html
										}).open(map,this);
							});
							markerIndexObject++;
						}
					}
				}else{
					document.getElementById('btnViewIcon').innerHTML = "tampil objek";
					var first = 0;
					var lengthMarker = markers.length;
					if(markerIndexObject > markerIndexNode){
						first = markerIndexNode;
					}else{
						lengthMarker = markerIndexObject;
					}
					for(var i=first; i<lengthMarker; i++){
						markers[i].setMap(null);
					}
					markerIndexObject = 0;
					firstObject = false;
				}
			}

			var polyLine = [];
			var shortLine = [];
			
			function viewPath(){
				for(i=0; i<polyLine.length; i++){
					polyLine[i].setMap(null);
				}
				polyLine = [];
				//bisa dibikin beda kalau pathnya lebih dari 1 tabel.
				for(var i=0; i<object.paths.length; i++){			
						//if(object.paths[i].rute == "mhy"){
					var poly = new google.maps.Polyline({
						path: [
							{lat:Number(object.nodes[object.paths[i].fnode].x),lng:Number(object.nodes[object.paths[i].fnode].y)},
							{lat:Number(object.nodes[object.paths[i].tnode].x),lng:Number(object.nodes[object.paths[i].tnode].y)}
						],
						strokeColor: "#4d9900",
						strokeOpacity: 1.0,
						strokeWeight: 3						
					});
					polyLine.push(poly);
					//}
				}
				
				for(i=0; i<polyLine.length; i++){
					polyLine[i].setMap(map);
				}
				
			}
			
			var marker;
			
			function initialize(){
				var mapProp = {
					center: myCenter,					
					zoom:15,
					mapTypeId:google.maps.MapTypeId.ROADMAP
				};
				
				map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
				map.setOptions({draggableCursor:'default'});
				
				google.maps.event.addListener(map, 'click', function(event) {
					placeMarker(event.latLng);
				});
				google.maps.event.addListener(map,'mousemove',function(event) {
					document.getElementById('latspan').innerHTML = event.latLng.lat();
					document.getElementById('lngspan').innerHTML = event.latLng.lng();
				});
				
				viewPath();			
			}
			
			function placeMarker(location){
				marker = new google.maps.Marker({
					position: location,
					map: map,
				});
				var infowindow = new google.maps.InfoWindow({
					content: 
						'<form method="post">'+
							'<table>'+
							'<tr>'+
								'<input id="id_node" hidden="true" type="text" name="id_node" value="'+object.nodes.length+'"/>'+
								'<td>Info:</td> <td><input id="info_node" type="text" name="info_node"/></td>'+
							'</tr>'+
							'<tr>'+
								'<td>Status:</td> <td><select id="status_node" name="status_node"><option value="track">track</option>'+
								'<option value="object">object</option></select></td>'+
							'</tr>'+
							'<tr>'+
								'<td>Jenis:</td> <td><select id="jenis" name="jenis"><option value="angkot">angkot</option>'+
								'<option value="bus">bus</option></select></td>'+
							'</tr>'+							
							'<tr>'+
								'<td>Latitude:</td><td><input id="x_node" type="text" name="x_node" value="'+location.lat()+'" readonly="true"/></td>'+
							'</tr>'+
							'<tr>'+
								'<td>Longitude:</td><td><input id="y_node" type="text" name="y_node" value="'+location.lng()+'" readonly="true"/></td>'+
							'</tr>'+
							'</table>'+
							'<br><a href="javascript:;"><button type="button" onClick="createNode();">submit</button></a>'+
						'</form>'
				});
				infowindow.open(map,marker);
				google.maps.event.addListener(infowindow,'closeclick',function(){
					marker.setMap(null);
				});
			}
			google.maps.event.addDomListener(window, 'load', initialize);
			//ajax
			function createNode(){
				$.ajax({
					type: "POST",
					url: 'Data.php',
					data:{action:'createNode',id:$('#id_node').val(),info:$('#info_node').val(),status:$('#status_node').val(),
						x:$('#x_node').val(),y:$('#y_node').val(),jenis:$('#jenis').val(),price:$('#price').val()},
					success:function(output) {
						data = jQuery.parseJSON(output);
						marker.setMap(null);
						//infowindow.close();
						refreshObject();
						data = "";
						for(var i=0; i<object.nodes.length; i++){
							data+='<option value="'+i+'">'+object.nodes[i].info+'</option>';
						}
						$('#node_path_1').html(data);
						$('#node_path_2').html(data);
					}
				});
			}
			
			function createPath(){
				$.ajax({
					type: "POST",
					url: 'Data.php',
					data:{action:'createPath',node1:$('#node_path_1').val(),node2:$('#node_path_2').val(),rute:$('#rute').val(),const_perkm: const_perkm},
					success:function(output) {
						refreshObject();
						for(i=0; i<polyLine.length; i++){
							polyLine[i].setMap(null);
						}
						viewPath();
						data = "";
						for(var i=0; i<object.paths.length; i++){
							data += '<option value='+object.paths[i].id+'>'+object.paths[i].id+'</option>';
						}
						$('#id_path').html(data);
					}
				});
			}
			
			function deletePath(){
				$.ajax({
					type: "POST",
					url: 'Data.php',
					dataType: 'json',
					data:{action:'deletePath',id:$('#edit_path').val()},
					success:function(output) {
						refreshObject();
						for(i=0; i<polyLine.length; i++){
							polyLine[i].setMap(null);
						}
						viewPath();
						data = "";
						for(var i=0; i<object.paths.length; i++){
							data+= '<option value='+object.paths[i].id+'>'+object.paths[i].id+'</option>';
						}
						$('#id_path').html(data);
					}
				});
			}
			 
			function refreshObject(){
				$.ajax({
					async: false,
					type: "POST",
					url: "Data.php",
					dataType: 'json',
					data: {action:"getData"},
					success: function(output){
						object = output;
					}
				});
			}
		</script>
	</head>
	<body>
		<div style="float: left; margin-left: 50px; width: 600px;">
			<button onClick="viewAllNode();" id="btnViewAllNode">tampil node</button>
			<button onClick="viewObject();" id="btnViewIcon">tampil objek</button>
			<br/>
			<br/>
			<br/>
			<br/>
			<div id="googleMap" style="width:1200px; height:500px;"></div>
			<div>Lattitude: <span id="latspan"></span></div> 
			<div>Longitude: <span id="lngspan"></span></div>
			<div><button id="id_btn" onClick="triggerBtn();">atur jalur</button></div>
			<div id="form_path">
				<div style="border: 1px #000 solid; width: 300px; float: left; ">
					<span> Add Path</span>
					<form method="post">
						<div> 
							<span> Node 1: </span>
							<select name="node_path_1" id="node_path_1">
								<script>
									//for(var i=144; i<object.nodes.length; i++){
									for(var i=0; i<object.nodes.length; i++){
										document.write('<option value="'+i+'">'+object.nodes[i].info+'</option>');
									}
								</script>
							</select>
						</div>
						<div> 
							<span> Node 2: </span>
							<select name="node_path_2" id="node_path_2">
								<script>
									// for(var i=144; i<object.nodes.length; i++){
									for(var i=0; i<object.nodes.length; i++){
										document.write('<option value="'+i+'">'+object.nodes[i].info+'</option>');
									}
								</script>
							</select>
						</div>
						<div>
							<span>
								rute : 	
							</span>
							<input style="width:100px;" type="text" id="rute" name="rute" value=""/>
						</div>
						<!--<input type="submit" name="btn_path" value="Submit"/>-->
						<button type="button" name="btn_path" id="btn_path" onClick="createPath()">Submit</button>
					</form>
				</div>
				<div style="border: 1px #000 solid; width: 240px; float: left; margin-left: 5px;">
					<span> View Path</span>
					<form method="post">
						<div> 
							<span> ID: </span>
							<select id="id_path" name="id_path">
								<script>
									for(var i=0; i<object.paths.length; i++){
										document.write('<option value='+object.paths[i].id+'>'+object.paths[i].id+'</option>');
									}
								</script>
							</select>
						</div>
						<div> 
							<span> Node 1: </span>
							<span id="node_path1">
								<script>
									if(object.paths.length != 0){
										document.write(object.nodes[object.paths[0].fnode].info);
									}
								</script>
							</span>
						</div>
						<div> 
							<span> Node 2: </span>
							<span id="node_path2">
								<script>
									if(object.paths.length != 0){
										document.write(object.nodes[object.paths[0].tnode].info);
									}
								</script>
							</span>
						</div>
						<div> 
							<span> Price: </span>
							<input style="width:100px;" type="text" id="price_path" name="price" readonly="true" value=""/>
							<script>
								if(object.paths.length != 0){
									price = object.paths[0].distance * const_perkm;
									$('#price_path').val(price);
								}
							</script>
							<!--<input type="text" id="edit_path" name="edit_path" hidden="true"/>-->
						</div>
						<button type="button" name="btn_delete_path" onClick="deletePath();">Delete</button>
					</form>
				</div>
			</div>
		</div>
		<div style="left; margin-left: 100px;">
			<br/>			
			Penelusuran jangkauan tarif transportasi umum dari objek :
			<select id="id_shortest" name="id_shortest">
				<script>
					for(var i=0; i<object.nodes.length; i++){
						if(object.nodes[i].status == 'object'){
							document.write('<option value='+object.nodes[i].id+'>'+object.nodes[i].info+'</option>');
						}
					}
				</script>
			</select>
			<button type="button" onClick="cariRute()">Cari</button>
			<div id="distanceShort"></div>
			<br/>			
		</div>
		<script>
		function cariRute(){
				$.ajax({
					async: false,
					type: "POST",
					url: "Data.php",
					dataType: 'json',
					data: {action:"cariRute",id:$('#id_shortest').val()},
					success: function(output){
						$('#distanceShort').html("harga : Rp. "+output.price);
						var node1 = output.node[0];
						var path = [];
						var ind = 0;
						for(var i=1; i<output.node.length; i++){
							for(var j=0; j<object.paths.length; j++){
								if(object.paths[j].fnode == node1['id'] && object.paths[j].tnode == output.node[i]['id']){
									path[ind] = object.paths[j];
									ind++;
									break;
								}else if(object.paths[j].tnode == node1['id'] && object.paths[j].fnode == output.node[i]['id']){
									path[ind] = object.paths[j];
									ind++;
									break;
								}
							}
							node1 = output.node[i];
						}
						for(i=0; i<shortLine.length; i++){
							shortLine[i].setMap(null);
						}
						shortLine = [];
						for(var i=0; i<path.length; i++){							
								var poly = new google.maps.Polyline({
									path: [
										{lat:Number(object.nodes[path[i].fnode].x),lng:Number(object.nodes[path[i].fnode].y)},
										{lat:Number(object.nodes[path[i].tnode].x),lng:Number(object.nodes[path[i].tnode].y)}
									],
								strokeColor: "#00FF00",
								strokeOpacity: 1.0,
								strokeWeight: 3
								});
								shortLine.push(poly);							
						}
						for(i=0; i<shortLine.length; i++){
							shortLine[i].setMap(map);
						}
					}
				});
			}
			$('#id_path').on('change', function() {
				$.ajax({
					type: "POST",
					url: 'Data.php',
					data:{action:'getPath ',id:$('#id_path').val()},
					success:function(output) {
						data = jQuery.parseJSON(output);
						$('#node_path1').html(data.fnode);
						$('#node_path2').html(data.tnode);
						price = data.distance * const_perkm;
						$('#price_path').val(price);
						$('#edit_path').val(data.id);
						$('#price_path').removeAttr('readonly');
					}
				});
			});
		</script>
	</body>
</html>
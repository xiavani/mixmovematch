<!DOCTYPE html>
<html lang="en">
<head>
	<title>CodeByte | Mixmovematchx</title>
	<meta charset="utf-8">
	<meta name="author" content="Xiavani">
	<meta name="describe" content="Personal website">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Archivo+Black&display=swap" rel="stylesheet">
	<link href="assets/css/all.css" rel="stylesheet">
	<link href="assets/css/all.min.css" rel="stylesheet">
	<link href="assets/css/fontawesome.css" rel="stylesheet">
  <link href="assets/css/brands.css" rel="stylesheet">
  <link href="assets/css/solid.css" rel="stylesheet">
	<link href="assets/css/core.css" rel="stylesheet">
	<script>
	var icon_hold = '<i class="far fa-square"></i>';
	var icon_process = '<i class="fas fa-spinner fa-pulse"></i>';
	var icon_done = '<i class="fas fa-check-square"></i>';
	var icon_error = '<i class="fas fa-times-circle"></i>';

	var bgcolor_hold = "none";
	var bgcolor_process = "#FF6347";
	var bgcolor_done = "#62A140";
	var bgcolor_error = "#E20338";

	var error_status = '';

	var output_result = '';
	output_result += '<h3 style="text-align: left;  padding-left:10px;">TABELA CROSSDOCK 2020</h3>';
	output_result += '<table class="result_table">';
	output_result += '<tr class="head">';
	output_result += '<td>ID</td>';
	output_result += '<td>GINC</td>';
	output_result += '<td>WAGA RAPORT</td>';
	output_result += '<td>WAGA MIXMOVE</td>';
	output_result += '<td>STATUS</td>';
	output_result += '<td>DETALE</td>';
	output_result += '</tr>';

	var error_code_90 = '';
	error_code_90 += '<h4><span style="color: red;">Błąd 90 - Nie ma żadnych danych do zaimportowania!</span></h4>';
	error_code_90 += '<h5>Aplikacja nie znalazła żadnych danych do zaimportowania. Upewnij się, że dane do kolumny GINC i WAGA zostaly wklejone poprawnie. Naciśnij przycisk "Reset" aby spróbować ponownie.</h5>';

  var error_code_91 = '';
	error_code_91 += '<h4><span style="color: red;">Błąd 91 - Ilość rekordów w kolumnach jest niezgodna!</span></h4>';
	error_code_91 += '<h5>Aplikacja wykryła, że ilość rekordów w kolumnie GINC i WAGA jest niezgodna. Oznacza to, że w przypadku zaimportowania np. 30 rekordów GINC i 29 rekordów WAGA, może nastąpić niepoprawne przydzielenie rekordów GINC do rekordów WAGA. </h5>';

  var error_code_92 = '';
	error_code_92 += '<h4><span style="color: red;">Błąd 92 - Nieprawidłowy numer GINC!</span></h4>';
	error_code_92 += '<h5>Aplikacja wykryła, że w kolumnie GINC znajduje się przynajmniej jeden nieprawidłowy numer (np. zbyt mało cyfr). Numer GINC powinien składać się z 17 znaków. Praktyka ta ma na celu wyemilinowanie nieprawidłowych zapytań do serwera WWW.</h5>';

	var error_code_00 = '';
	error_code_00 += '<h4><span style="color:green">Procesowanie zakończone pomyślnie!</span><br>Klikjnij "Dalej" aby kontynuować.</h4>';

	var excel_ginc;
	var excel_ginc_converted;
	var excel_weight;
	var excel_weight_converted;
	var mixmove_weight = [];
	var excel_status = [];

  var step = 1;

	var lock_button = 'locked';

	var lock_request = 'unlocked';

	var i_global = 0;

	var request_time = 0;
	var request_time_bool = 'locked';

	var request_incorrect = 0;
	var request_correct = 0;
	var request_conn_errorn = 0;

	function start(){
		document.getElementById("form_button_nextstep").style.backgroundColor = "#939598";
		check_cors();
	}

	function check_cors(){
		var cors_request = new XMLHttpRequest();
		cors_request.open('GET', 'https://www.google.com');
		cors_request.responseType = "";
		cors_request.send();

		cors_request.onload = function() {
		if (cors_request.status != 200) {
			alert(`Error ${cors_request.status}: ${cors_request.statusText}`);
			document.getElementById("id_cors").style.backgroundColor = "#EE3D48";
			document.getElementById("id_cors").value = 'Nieaktywne';
			document.getElementById("id_authentication").style.backgroundColor = "#EE3D48";
			document.getElementById("id_authentication").value = 'Disabled';
		} else {
			document.getElementById("id_cors").style.backgroundColor = "#8CBA51";
			document.getElementById("id_cors").value = 'Aktywne';
			check_authentication();
		}
		}

		cors_request.onerror = function() {
			document.getElementById("id_cors").style.backgroundColor = "#EE3D48";
			document.getElementById("id_cors").value = 'Nieaktywne';
			document.getElementById("id_authentication").style.backgroundColor = "#EE3D48";
			document.getElementById("id_authentication").value = 'Nieaktywne';
		}
	}

	function check_authentication(){
		var id_source_code;
		var id_search;

		var auth_request = new XMLHttpRequest();
		auth_request.open('GET', 'https://www.mixmovematch.com/OutboundConsignmentInfo/Index');
		auth_request.responseType = "";
		auth_request.send();

		auth_request.onload = function() {
		if (auth_request.status != 200) {
			alert(`Error ${auth_request.status}: ${auth_request.statusText}`);
		} else {
			id_source_code = auth_request.responseText;
			id_search = id_source_code.search("<title>Outbound Consignment Information</title>");
			if(id_search == -1){
				document.getElementById("id_authentication").style.backgroundColor = "#EE3D48";
				document.getElementById("id_authentication").value = 'Nieaktywne';
			} else {
				document.getElementById("id_authentication").style.backgroundColor = "#8CBA51";
				document.getElementById("id_authentication").value = 'Aktywne';
				document.getElementById("form_button_nextstep").style.backgroundColor = "#FF6347";
				lock_button = 'unlocked';
			}
		}}
	}

	function form_button_nextstep(){
		if(step < 3 && lock_button == 'unlocked'){
			step++;
			check_card();
		}
	}

	function request_time_x() {
		if(request_time_bool == 'unlocked'){
			request_time++;
			document.getElementById("x_05").innerHTML = request_time + " s.";
			setTimeout(request_time_x, 1000);
		}
	}

	function form_button_start(){
		if(lock_request == 'unlocked'){
			document.getElementById("form_button_start").style.backgroundColor = "#939598";
			document.getElementById("form_button_start").innerHTML = 'Rozpocznij sprawdzanie <i class="fas fa-spinner fa-pulse"></i>';
			document.getElementById("current_request").innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Aktualne zapytanie';
			request_time_bool = 'unlocked';
			request_time_x();
			request_01();
			lock_request = 'locked';
		}
	}

	function check_card(){
		if(step == 1){
		} else if(step == 2){
			lock_button = 'locked';
			document.getElementById("form_button_nextstep").style.backgroundColor = "#939598";
			document.getElementById('card_step_01').style.display = "none";
			document.getElementById('card_step_02').style.display = "block";
			document.getElementById('card_step_03').style.display = "none";

			document.getElementById('progress_01').style.color = "#41B619";
			document.getElementById('progress_01').style.borderBottom = "2px solid #41B619";
			document.getElementById('progress_02').style.color = "#FF6347";
			document.getElementById('progress_02').style.borderBottom = "2px solid #FF6347";
			document.getElementById('progress_03').style.color = "#939598";
			document.getElementById('progress_03').style.borderBottom = "2px solid #939598";
			processing_01();
		} else if(step == 3){
			document.getElementById('card_step_01').style.display = "none";
			document.getElementById('card_step_02').style.display = "none";
			document.getElementById('card_step_03').style.display = "block";

			document.getElementById('form_button_reset').style.display = "none"
			document.getElementById('form_button_nextstep').style.display = "none";
			document.getElementById('form_button_start').style.display = "block";

			document.getElementById('progress_02').style.color = "#41B619";
			document.getElementById('progress_02').style.borderBottom = "2px solid #41B619";
			document.getElementById('progress_03').style.color = "#FF6347";
			document.getElementById('progress_03').style.borderBottom = "2px solid #FF6347";
		}
	}

	function processing_01(){
		console.log("WERYFIKACJA DANYCH");
		console.log("");
		document.getElementById("background_01").style.backgroundColor = bgcolor_process;
		document.getElementById("icon_placeholder_01").innerHTML = icon_process;

		console.log("Pobieranie danych z formularza...");
		excel_ginc = document.getElementById("id_ginc").value;
		excel_weight = document.getElementById("id_weight").value;
		excel_ginc_converted = excel_ginc.split(/\n/);
		excel_weight_converted = excel_weight.split(/\n/);
		excel_ginc_length = excel_ginc_converted.length;
		excel_weight_length = excel_weight_converted.length;

		if(excel_ginc_converted.length == 1){
			if(excel_ginc_converted[0] == ""){
				console.log("Nie ma żadnych danych do zaimportowania!");
				setTimeout(processing_failed, 3000);
				error_status = "ERROR_90";
			} else {
				setTimeout(processing_02, 3000);
			}
		} else {
			setTimeout(processing_02, 3000);
		}
	}

	function processing_02(){
		console.log("Importowanie danych...");
		document.getElementById("background_01").style.backgroundColor = bgcolor_done;
		document.getElementById("icon_placeholder_01").innerHTML = icon_done;
		document.getElementById("background_02").style.backgroundColor = bgcolor_process;
		document.getElementById("icon_placeholder_02").innerHTML = icon_process;

		console.log("Sprawdzanie wsytępowania niespodziewanych rekordów...")
		while(excel_ginc_converted[excel_ginc_length-1] == ""){
			console.log("Removed GINC - " + excel_ginc_converted[excel_ginc_length-1]);
			excel_ginc_length--;
			excel_ginc_converted.pop();
		}

		while(excel_weight_converted[excel_weight_length-1] == ""){
			console.log("Removed WAGA - " + excel_weight_converted[excel_weight_length-1]);
			excel_weight_length--;
			excel_weight_converted.pop();
		}
		console.log("Sprawdzanie wsytępowania niespodziewanych rekordów zakończone!")

		console.log("Sprawdzanie sumy kontrolnej kolumn...")
		if(excel_ginc_length == excel_weight_length){
			console.log("Długość sumy kontrolej kolumn GINC oraz WEIGHT prawidłowa.");
			console.log("Importowanie zakończone sukcesem!");
			setTimeout(processing_03, 1000);
		} else {
			console.log("Importowanie zakończone niepowodzeniem!");
			console.log("Długość kolumny GINC oraz WEIGHT nieprawidłowa.");
			error_status = "ERROR_91";
			setTimeout(processing_failed, 1000);
		}
	}

	function processing_03(){
		document.getElementById("background_02").style.backgroundColor = bgcolor_done;
		document.getElementById("icon_placeholder_02").innerHTML = icon_done;
		document.getElementById("background_03").style.backgroundColor = bgcolor_process;
		document.getElementById("icon_placeholder_03").innerHTML = icon_process;

		console.log("Konwertowanie kolumny WEIGHT na FLOAT...");
		console.log("");
		for (var i=0; i<excel_weight_length; i++){
			excel_weight_converted[i] = excel_weight_converted[i].replace(",",".");
			excel_weight_converted[i] = parseFloat(excel_weight_converted[i]);
			console.log("Wiersz " + i + " = " + excel_weight_converted[i]);
		}
		console.log("");
		console.log("Konwertowanie zakończone!");

		console.log("Sprawdzanie poprawności klucza GINC...");
		for (var i=0; i<excel_ginc_converted.length; i++){
			if(excel_ginc_converted[i].length > 17){
				while(excel_ginc_converted[i].charAt(0) == " "){
					excel_ginc_converted[i] = excel_ginc_converted[i].slice(1);
				}
			}
			if(excel_ginc_converted[i].length > 17){
				excel_ginc_converted[i] = excel_ginc_converted[i].slice(0, 17);
			}

			if(excel_ginc_converted[i].length < 17){
				error_status = "ERROR_92";
			}
		}

		console.log("");
		if(error_status == "ERROR_92"){
			setTimeout(processing_failed, 3500);
		} else {
			setTimeout(processing_04, 3500);
		}
	}

	function processing_04(){
		console.log("Zapisywanie zmian...");
		console.log("Zmiany zapisane pomyślnie!");
		console.log("");
		document.getElementById("background_03").style.backgroundColor = bgcolor_done;
		document.getElementById("icon_placeholder_03").innerHTML = icon_done;
		document.getElementById("background_04").style.backgroundColor = bgcolor_process;
		document.getElementById("icon_placeholder_04").innerHTML = icon_process;
		setTimeout(processing_05, 2000);
	}

	function processing_05(){
		document.getElementById("background_04").style.backgroundColor = bgcolor_done;
		document.getElementById("icon_placeholder_04").innerHTML = icon_done;
		document.getElementById("background_05").style.backgroundColor = bgcolor_process;
		document.getElementById("icon_placeholder_05").innerHTML = icon_process;
		setTimeout(processing_success, 1000);
	}

	function processing_failed(){
		console.log("Aplikacja nie mogła wykonać wszystkich zadań.");
		if(error_status == "ERROR_90"){
			document.getElementById("background_01").style.backgroundColor = bgcolor_error;
			document.getElementById("icon_placeholder_01").innerHTML = icon_error;
			document.getElementById("icon_placeholder_02").innerHTML = icon_error;
			document.getElementById("icon_placeholder_03").innerHTML = icon_error;
			document.getElementById("icon_placeholder_04").innerHTML = icon_error;
			document.getElementById("icon_placeholder_05").innerHTML = icon_error;
			document.getElementById("processing_info").innerHTML = error_code_90;
		} else if(error_status == "ERROR_91"){
			document.getElementById("background_02").style.backgroundColor = bgcolor_error;
			document.getElementById("icon_placeholder_02").innerHTML = icon_error;
			document.getElementById("icon_placeholder_03").innerHTML = icon_error;
			document.getElementById("icon_placeholder_04").innerHTML = icon_error;
			document.getElementById("icon_placeholder_05").innerHTML = icon_error;
			document.getElementById("processing_info").innerHTML = error_code_91;
		} else if(error_status == "ERROR_92"){
			document.getElementById("background_03").style.backgroundColor = bgcolor_error;
			document.getElementById("icon_placeholder_03").innerHTML = icon_error;
			document.getElementById("icon_placeholder_04").innerHTML = icon_error;
			document.getElementById("icon_placeholder_05").innerHTML = icon_error;
			document.getElementById("processing_info").innerHTML = error_code_92;
		}
	}

	function processing_success(){
		document.getElementById("background_05").style.backgroundColor = bgcolor_done;
		document.getElementById("icon_placeholder_05").innerHTML = icon_done;
		document.getElementById("form_button_nextstep").style.backgroundColor = "#FF6347";
		lock_button = 'unlocked';
		console.log("WERYFIKACJA DANYCH ZAKOŃĆZONA POMYŚLNIE!");
		document.getElementById('processing_info').innerHTML = error_code_00;
		document.getElementById('x_01').innerHTML = excel_ginc_converted.length;
		document.getElementById('y_01').innerHTML = "0";
		document.getElementById('y_02').innerHTML = excel_ginc_converted.length;
	}

	function request_01() {
		if(i_global < excel_ginc_length){
			request_02();
		} else {
			request_success();
		}
	}

	function request_02() {
		console.log("");
		console.log("Zapytanie Nr. " + i_global);
		document.getElementById('y_01').innerHTML = i_global+1;
		document.getElementById("")
		var xhr = new XMLHttpRequest();
		xhr.open('GET', 'https://www.mixmovematch.com/OutboundConsignmentInfo/Details?GINC=' + excel_ginc_converted[i_global]);
		xhr.responseType = "";
		xhr.send();

		xhr.onload = function() {
		if (xhr.status != 200){ // analyze HTTP status of the response
			request_conn_errorn++;
			document.getElementById('x_06').innerHTML = 'Niepowodzenie';
			document.getElementById('x_04').innerHTML = request_conn_errorn;
			i_global++;
			request_01();
		} else { // show the result
			document.getElementById('x_06').innerHTML = 'Powodzenie';

			request_sourcecode = xhr.responseText;
			var request_sourcecode_find = request_sourcecode.search("<legend>OutboundConsignment Resume</legend>");
			request_sourcecode = request_sourcecode.slice(request_sourcecode_find-1, request_sourcecode_find+3000);
			request_sourcecode_find = request_sourcecode.search("</table>");
			request_sourcecode = request_sourcecode.slice(1, request_sourcecode_find+8);

			request_sourcecode = request_sourcecode.split(/\r?\n/);

			request_sourcecode_weight = request_sourcecode[19];
			request_sourcecode_find = request_sourcecode_weight.search('<td class="field" colspan="1">');
			request_sourcecode_weight = request_sourcecode_weight.slice(request_sourcecode_find+30, 300);
			request_sourcecode_find = request_sourcecode_weight.search("</td>");
			request_sourcecode_weight = request_sourcecode_weight.slice(0, request_sourcecode_find);

			request_sourcecode_weight = parseFloat(request_sourcecode_weight);
			mixmove_weight[i_global] = request_sourcecode_weight;

			console.log("GINC - " + excel_ginc_converted[i_global]);
			console.log("Waga M3 - " + mixmove_weight[i_global]);
			console.log("Waga Raport - " + excel_weight_converted[i_global]);

			document.getElementById('x_07').innerHTML = excel_ginc_converted[i_global];
			document.getElementById('x_08').innerHTML = excel_weight_converted[i_global];
			document.getElementById('x_09').innerHTML = mixmove_weight[i_global];

			if(mixmove_weight[i_global] == excel_weight_converted[i_global]){
				excel_status[i_global] = 1;
				console.log("ZGODNE");
				document.getElementById('x_10').innerHTML = 'Zgodne';
				request_correct++;
				document.getElementById('x_02').innerHTML = request_correct;
				output_result += '<tr class="request_code_0">';
				output_result += '<td>' + (i_global+1) + '</td>';
				output_result += '<td>' + excel_ginc_converted[i_global] + '</td>';
				output_result += '<td>' + excel_weight_converted[i_global] + '</td>';
				output_result += '<td>' + mixmove_weight[i_global] + '</td>';
				output_result += '<td style="color: green;"><b><i class="fas fa-check-circle"></i> ZGODNE</b></td>';
				output_result += '<td><a href="https://www.mixmovematch.com/OutboundConsignmentInfo/Details?GINC=' + excel_ginc_converted[i_global] + '" target="_blank">DETALE</a></td>';
				output_result += '</tr>';
			} else {
				excel_status[i_global] = 0;
				console.log("NIEZGODNE");
				document.getElementById('x_10').innerHTML = 'Niezgodne';
				request_incorrect++;
				document.getElementById('x_03').innerHTML = request_incorrect;
				output_result += '<tr class="request_code_1">';
				output_result += '<td>' + (i_global+1) + '</td>';
				output_result += '<td>' + excel_ginc_converted[i_global] + '</td>';
				output_result += '<td>' + excel_weight_converted[i_global] + '</td>';
				output_result += '<td>' + mixmove_weight[i_global] + '</td>';
				output_result += '<td style="color:red;"><b><i class="fas fa-times-circle"></i> NIEZGODNE</b></td>';
				output_result += '<td><a href="https://www.mixmovematch.com/OutboundConsignmentInfo/Details?GINC=' + excel_ginc_converted[i_global] + '" target="_blank">DETALE</a></td>';
				output_result += '</tr>';
			}

			i_global++;

			request_01();
		}

		xhr.onerror = function () {
  	console.log("** An error occurred during the transaction");
		};
	};}

	function request_success(){
		console.log("");
		console.log("Proces zakończony!");
		console.log("Ilość błędów = " + request_incorrect);
		request_time_bool = 'locked';
		output_result += "</table>";
		document.getElementById("form_button_start").style.display = "none";
		document.getElementById("form_button_generate").style.display = "block";
		document.getElementById("progress_03").innerHTML = '<i class="fas fa-check-circle"></i> Sprawdzanie zakończone';
		document.getElementById('progress_03').style.color = "#41B619";
		document.getElementById('progress_03').style.borderBottom = "2px solid #41B619";
		document.getElementById('current_request').innerHTML = '<i class="fas fa-check-circle"></i> Sprawdzanie zakończone!';
	}

	function form_button_generate() {
			document.getElementById('card_step_01').style.display = "none";
			document.getElementById('card_step_02').style.display = "none";
			document.getElementById('card_step_03').style.display = "none";

			document.getElementById("card_result").style.display = "block";
			document.getElementsByClassName("main_card")[0].style.display = "none";
			document.getElementById('result_content').innerHTML = output_result;
	}

	function show_incorrect(){
		var x = document.getElementsByClassName("request_code_0");
		var i;
		for (i = 0; i < x.length; i++) {
  		x[i].style.display = "none";
		}
	}

	</script>
	<style>
	#card_step_01 {
		display: block;
	}
	#card_step_02 {
		display: none;
	}
	#card_step_03 {
		display: none;
	}
	</style>
</head>
<body onload="start()">
<center>


<div class="header">
  <div class="header_limit">
  	<a href="#"><img src="assets/imgs/logoCodeceyCom.png" class="header_logo"/></a>
  	<div class="header_path"><i style="padding-right: 5px;" class="fas fa-lock"></i> Applications / Mixmovematch </a></div>
  </div>
</div>

<div class="section">
	<div class="section_limit">
		<div class="main_card">

			<div class="progress_bar">
				<div id="progress_01"><i class="fas fa-info-circle"></i> Wprowadzanie danych</div>
				<div id="progress_02"><i class="fas fa-question-circle"></i> Weryfikacja danych</div>
				<div id="progress_03"><i class="fas fa-check-circle"></i> Rozpocznij sprawdzanie</div>
			</div>

			<div id="card_step_01">
				<div id="card_01">
					<h3 class="form_container_h"><i class="fas fa-pen-square"></i> Wprowadź dane</h3>
					<p class="form_container_p">Typ załącznika</p>
					<select name="attachment_type" class="form_input">
						<option value="Text">Plik Tekstowy</option>
  					<option value="Excel">Plik Excel</option>
  					<option value="Word">Plik Word</option>
					</select>
					<p class="form_container_p">Data</p>
					<input type="month" value="2020-01" class="form_input">
					<p class="form_container_p">Nazwa użytkownika</p>
					<input type="text" value="Administracja" class="form_input">
					<p class="form_container_p">Wklej dane</p>
					<textarea class="form_textarea" style="margin-right: 20px; float:left;" placeholder="Wklej kolumnę GINC" id="id_ginc"></textarea>
					<textarea class="form_textarea" placeholder="Wklej kolumnę WAGA" id="id_weight"></textarea>
				</div>
				<div id="card_02">
					<h3 class="form_container_h"><i class="fas fa-pen-square"></i> Ustawienia połączenia</h3>
					<p class="form_container_p">URL Serwera</p>
					<input type="text"  value="https://www.mixmovematch.com" disabled class="form_input"/>
					<p class="form_container_p">Cross-Origin Resource Sharing</p>
					<input type="text" value="Sprawdzanie..." id="id_cors" disabled class="form_input"/>
					<p class="form_container_p">Autoryzacja połączenia</p>
					<input type="text" value="Sprawdzanie..." id="id_authentication" disabled class="form_input"/>
				</div>
				<div id="card_03">
					<h3 class="form_container_h"><i class="fas fa-pen-square"></i> Autoryzacja</h3>
					<p class="form_container_p">Jeżeli <i>"Cross-Origin Resource Sharing"</i> lub <i>"Autoryzacja połączenia"</i> są nieaktywne, nie będzie można rozpocząć weryfikacji. Sprawdź <a href="documentation" style="border-bottom: 1px solid #FF6347; color:#FF6347;">dokumentację</a> jeżeli chcesz się dowiedzieć więcej. <i>"Autoryzację połączenia"</i> uzyskasz logując się do Mixmovematch.</p>
					<a href="https://www.mixmovematch.com/" target="_blank"><div id="xax">Zaloguj się <i class="fas fa-user-tag"></i></div></a>
				</div>
			</div>

			<div id="card_step_02">
				<div id="card_04">
					<div class="progress_container" id="background_01">
						<h3 class="progress_container_h">
							<span id="icon_placeholder_01"><i class="far fa-square"></i></span>
							<span>1. Weryfikacja</span>
						</h3>
						<p class="progress_container_p">Weryfikowanie danych wejściowych oraz sprawdzanie spójności danych.</p>
					</div>
					<div class="progress_container" id="background_02">
						<h3 class="progress_container_h">
							<span id="icon_placeholder_02"><i class="far fa-square"></i></span>
							<span>2. Transferowanie</span>
						</h3>
						<p class="progress_container_p">Transfer danych wejściowych do tablicy.</p>
					</div>
					<div class="progress_container" id="background_03">
						<h3 class="progress_container_h">
							<span id="icon_placeholder_03"><i class="far fa-square"></i></span>
							<span>3. Konwertowanie</span>
						</h3>
						<p class="progress_container_p">Konwertowanie danych oraz sprawdzanie poprawności GINC oraz Weight.</p>
					</div>
					<div class="progress_container" id="background_04">
						<h3 class="progress_container_h">
							<span id="icon_placeholder_04"><i class="far fa-square"></i></span>
							<span>4. Zapisywanie zmian</span>
						</h3>
						<p class="progress_container_p">Zapisywanie danych w formie umożliwiającej zapytanie do serwera.</p>
					</div>
					<div class="progress_container" id="background_05">
						<h3 class="progress_container_h">
							<span id="icon_placeholder_05"><i class="far fa-square"></i></span>
							<span>5. Gotowe</span>
						</h3>
						<p class="progress_container_p">Wszystkie procesy został zakończone pomyślnie.</p>
					</div>
				</div>
				<div id="card_05">
					<img src="assets/imgs/mixmove-grey.png" class="mixmove_anim">
				</div>
				<div id="card_06">
					<h3><i class="fas fa-exclamation-circle"></i> Informacje</h3>
					<div id="processing_info"><h4>Procesowanie...<br>Po zakończonej weryfikacji będzie można przejść dalej.<br></h4></div>
				</div>
			</div>

			<div id="card_step_03">
				<div id="card_07">
					<h3 class="form_container_h"><i class="fas fa-pen-square"></i> Panel sprawdzania</h3>
					<table class="table_control">
						<tr><td><i class="fas fa-angle-right"></i> Serwer WWW</td><td>Mixmovematch</td></tr>
						<tr><td><i class="fas fa-angle-right"></i> Zaimportowane rekordy</td><td id="x_01">0</td></tr>
						<tr><td><i class="fas fa-angle-right"></i> Ilość zgodnych rekordów</td><td id="x_02">0</td></tr>
						<tr><td><i class="fas fa-angle-right"></i> Ilość niezgodnych rekordów</td><td id="x_03">0</td></tr>
						<tr><td><i class="fas fa-angle-right"></i> Ilość odrzuonych połączeń</td><td id="x_04">0</td></tr>
						<tr><td><i class="fas fa-angle-right"></i> Czas trwania</td><td id="x_05">0</td></tr>
						<tr><td></td><td></td></tr>
						<tr><td><i class="fas fa-angle-right"></i> Typ załącznika</td><td>Plik tekstowy</td></tr>
						<tr><td><i class="fas fa-angle-right"></i> Data</td><td>Styczeń 2020</td></tr>
						<tr><td><i class="fas fa-angle-right"></i> Nazwa użytkownika</td><td>Administrator</td></tr>
					</table>
				</div>
				<div id="card_08">
					<img src="assets/imgs/mixmove-grey.png" class="mixmove_anim" style="padding-top:85px;">
				</div>
				<div id="card_09">
					<h3 class="form_container_h"><span id="current_request">Aktualne zapytanie</span>
						<div style="float:right;"><span id="y_01"></span> / <span id="y_02"></span></div>
					</h3>
					<table class="table_control">
						<tr><td><i class="fas fa-angle-right"></i> Status zapytania</td><td id="x_06"></td></tr>
						<tr><td><i class="fas fa-angle-right"></i> GINC</td><td id="x_07"></td></tr>
						<tr><td><i class="fas fa-angle-right"></i> Waga z raportu</td><td id="x_08"></td></tr>
						<tr><td><i class="fas fa-angle-right"></i> Waga z Mixmove</td><td id="x_09"></td></tr>
						<tr><td><i class="fas fa-angle-right"></i> Staus wartości</td><td id="x_10"></td></tr>
					</table>
				</div>
			</div>

			<div class="main_card_footer">
				<a href="#"><div id="form_button_nextstep" onClick="form_button_nextstep();">Dalej <i class="fas fa-arrow-alt-circle-right"></i></div></a>
				<a href=""><div id="form_button_reset">Reset <i class="fas fa-window-restore"></i></div></a>
				<a href="#"><div id="form_button_start" onClick="form_button_start();">Rozpocznij sprawdzanie <i class="fas fa-clipboard-check"></i></div></a>
				<a href="#"><div id="form_button_generate" onClick="form_button_generate();">Generuj raport <i class="fas fa-file-excel"></i></div></a>
			</div>
		</div>

		<div id="card_result">
			<div class="result_bar">
				<a href=""><div id="form_button_xx">Rozpocznij ponownie</div></a>
				<a href="#"><div id="form_button_xx" onclick="show_incorrect()">Pokaż tylko NIEZGODNE</div></a>
			</div>
			<div id="result_content"></div>
			<section>
		</div>

	</div>
</div>

<div class="footer">
	<div class="footer_limit">

		<div class="container_form">
			<p class="head_text">Contact Me</p>
			<form>
				<div class="box_01">
					<input type="text" placeholder="Name" class="input"/>
					<input type="text" placeholder="Email" class="input"/>
					<input type="text" placeholder="Subject" class="input"/>
				</div>
				<div class="box_02">
					<textarea class="textarea" placeholder="Message" cols="30" rows="5"></textarea>
				</div>
				<button type="submit" class="submit"><span class="visiblex"><i class="fas fa-share-alt-square"></i> Send</span></button>
			</form>
		</div>

		<div class="container_aboutme">
			<p class="head_text">About Me</p>
			<p class="text_aboutme">Whether you have a question about business matters,
				preparing a website for you or anything else, I'm ready to answer all of it.
				I'm always trying to respond within a few days.</p>
		</div>

		<div class="container_social">
			<p class="head_text">Social Media</p>
			<a href="#"><p class="text_social"><i class="fab fa-facebook-square"></i> Facebook</p></a>
			<a href="#"><p class="text_social"><i class="fab fa-linkedin"></i> LinkedIn</p></a>
			<a href="#"><p class="text_social"><i class="fab fa-twitter-square"></i> Twitter</p></a>
			<a href="#"><p class="text_social"><i class="fab fa-git-square"></i> Github</p></a>
			<a href="#"><p class="text_social"><i class="fab fa-google-plus-square"></i> Gmail</p></a>
		</div>
	</div>
</div>

<div class="footer_copyright">
	<div class="footer_limit">
	<p class="copyright_text fleft">All rights reserved | Updated 2020.01 | <a href="#">Policy privacy</a></p>
	<p class="copyright_text fright">Created by Krzysztof Glab</p>
	<div class="clear"></div>
	</div>
</div>
</center>
</body>
</html>

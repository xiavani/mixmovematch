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

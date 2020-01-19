<!DOCTYPE html>
<html lang="en">
<head>
	<title>CodeByte | Mixmovematch</title>
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
	<script type="text/javascript" src="assets/js/code.js"></script>
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

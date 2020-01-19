<!DOCTYPE html>
<html lang="en">
<head>
	<title>CodeByte | Home</title>
	<meta charset="utf-8">
  <script>
  start();
  function start(){
    document.location.href = 'chrome_dws.bat';
    setTimeout(go_app, 500);
  }

  function go_app(){
    location.href = '../';
  }
  </script>
</html>

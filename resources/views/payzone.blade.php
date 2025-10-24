<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redirecting to Payzone...</title>
</head>
<body>
  <form id="openPaywall" action="<?php echo $paywallUrl; ?>" method="POST" >
    <input type="hidden" name="payload" value='<?php echo $payload; ?>' />
    <input type="hidden" name="signature" value="<?php echo $signature; ?>" />
  </form>

  <script type="text/javascript">
    document.getElementById("openPaywall").submit();
  </script>
</body>
</html>

<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php';
require_once '../../logic/session_la.logic.php';
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Ausbilder E-Mail-Verteiler | Deutsche Einradhockeyliga";
include '../../templates/header.tmp.php';
?>
<h2>Eintragung in den E-Mailverteiler der Schiedsrichterausbilder</h2>

<form action="https://ml.kundenserver.de/cgi-bin/mailinglist.cgi" method="POST" target="_blank">
  <input checked name="subscribe_r" type="radio" value="subscribe">
  Ja, ich möchte mich auf die Mailingliste ausbilder@einrad.hockey.
  <br />
  <input name="subscribe_r" type="radio" value="unsubscribe">
  Bitte streichen Sie mich wieder von der Mailingliste ausbilder@einrad.hockey.
  <br />
  Geben Sie Ihre E-Mail-Adresse ein:
  <br />
  <input maxlength="51" name="mailaccount_r" size="51" type="text">
  <br />
  Wiederholen Sie die eingegebene E-Mail-Adresse:
  <br />
  <input maxlength="51" name="mailaccount2_r" size="51" type="text">
  <br />
  <input type="SUBMIT" value="Absenden">
  <br />
  <input type="RESET" value="Zurücksetzen">
  <hr />
  <input name="FBMLNAME" type="hidden" value="ausbilder@einrad.hockey">
  <br />
  <input name="FBLANG" type="hidden" value="de">
  <br />
  <input name="FBURLERROR_L" type="hidden" value="https://ml.kundenserver.de/mailinglist/error.de.html">
  <br />
  <input name="FBURLSUBSCRIBE_L" type="hidden" value="https://ml.kundenserver.de/mailinglist/subscribe.de.html">
  <br />
  <input name="FBURLUNSUBSCRIBE_L" type="hidden" value="https://ml.kundenserver.de/mailinglist/unsubscribe.de.html">
  <br />
  <input name="FBURLINVALID_L" type="hidden" value="https://ml.kundenserver.de/mailinglist/invalid.de.html">
</form>

<?php include '../../templates/footer.tmp.php';
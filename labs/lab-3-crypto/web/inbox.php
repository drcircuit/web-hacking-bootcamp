<?php
session_start();
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html><html><body>
<h1>📫 Inbox – EvilCorp Internal</h1>
<p><strong>From:</strong> vault@evilcorp.local</p>
<p><strong>Subject:</strong> Encrypted note (temp save)</p>
<pre>
dmF1bHRfa2V5OnN1cGVyc2VjcmV0a2V5MTIzNDU2
</pre>
<p><em>P.S. Remember to delete this before launch.</em></p>
<p><strong>Flag:</strong> WCH{md5_was_a_mistake}</p>
</body></html>
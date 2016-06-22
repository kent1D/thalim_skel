<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>
<frameset>
    <frame>
    <frame>
    <noframes>
    <body>
    <p>This page uses frames. The current browser you are using does not support frames.</p>
    <?php
include_once 'thalim_skel_fonctions.php';
echo "<pre>";
print_r(thalim_zotspip_ids_ref("Mon texte avant; bordel : £JSI3D8UF@Mon texte après !!, avant la ref£JSI3e8UF@après"));
echo "\n";
print_r(thalim_zotspip_suffixes_ref("Mon texte avant; bordel : £JSI3D8UF@Mon texte après !!, avant la ref£JSI3e8UF@après"));
echo "\n";
print_r(thalim_zotspip_prefixes_ref("Mon texte avant; bordel : £JSI3D8UF@Mon texte après !!, avant la ref£JSI3e8UF@après"));
echo "</pre>";
echo thalim_zotspip_div_en_span("<div class=\"csl-bib-body\"><div class=\"csl-entry\">GROOS, Karl, <span style=\"font-style: italic;\">Einleitung in die Ästhetik</span>, Giessen, J. Ricker, 1892,  [En ligne : http://echo.mpiwg-berlin.mpg.de/ECHOdocuView?mode=imagepath&amp;url=/permanent/library/WQ0A40CY/pageimg].</div></div>");

	?>
    </body>
    </noframes>
</frameset>
</html>
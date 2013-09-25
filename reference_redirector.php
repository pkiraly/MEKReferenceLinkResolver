<?php
/**
 * Resolve page numbers as MEK URLs. It redirects the user agent to the corresponding web page inside MEK.
 * The numbers came from common reference works for old Hungarian books like 'Régi Magyar Nyomtatványok' or 'Régi Magyar Könyvtár'.
 * Some examples of valid inputs:
 *   rmny/353/1/3   (redirects to http://mek.oszk.hu/08800/08838/html/hu_b1_rmk-1-113a_338.html)
 *   RMNy/0353/1/0003A  (redirects to http://mek.oszk.hu/08800/08838/html/hu_b1_rmk-1-113a_338.html)
 *   RMNY/0353/1/0003A  (redirects to http://mek.oszk.hu/08800/08838/html/hu_b1_rmk-1-113a_338.html)
 *   RMK/1/0332/1/0003A (redirects to http://mek.oszk.hu/08800/08838/html/hu_b1_rmk-1-113a_338.html, since RMNy 353 = RMK I. 332)
 *   RMK/I/0332/1/0003A (redirects to http://mek.oszk.hu/08800/08838/html/hu_b1_rmk-1-113a_338.html)
 *
 * In case of problem it redirects to the default URL, which is http://mek.oszk.hu, but the admin can overwrite it with redefining DEFAULT_URL.
 *
 * Works together with the following URL Rewriter rule:
 *     RewriteRule ^((rmny|rmk)/.*)$ reference_redirector.php?q=$1 [NC,QSA]
 *
 * A full example of a .htaccess file
 * <IfModule mod_rewrite.c>
 *  RewriteEngine on
 *
 *  # Rewrite URLs of the form 'rmny/x' to the form 'reference_redirector.php?q=rmny/x'.
 *  # It is case insensitive
 *  RewriteCond %{REQUEST_FILENAME} !-f
 *  RewriteCond %{REQUEST_FILENAME} !-d
 *  RewriteRule ^((rmny|rmk)/.*)$ reference_redirector.php?q=$1 [NC,QSA]
 * </IfModule>
 *
 * It use several CSV files. Since this project is in early phase CSV usage is enough. Later we should use database queries.
 * rmk.csv: RMK identifiers to MEK identifiers (fields: RMK ID;MEK ID)
 * rmny.csv: RMNy identifiers to MEK identifiers (fields: RMNy ID;MEK ID)
 * 08838.csv: page numbers to MEK file names in MEK-8838 (fields: page identifier;MEK file name)
 *
 * If a new RMNy/RMK item come to MEK, we should create the <MEKID>.csv file, and update rmk.csv, rmny.csv files.
 *
 * Configuration:
 *   Put all CSV files into a common directory, and define this directory's relative path as CSV_DIR. The default is 'reference_redirector_csv'.
 */
include_once('reference_redirector.functions.php');

$url = resolve_url($_GET['q']);
header('Location: ' . $url);
exit;

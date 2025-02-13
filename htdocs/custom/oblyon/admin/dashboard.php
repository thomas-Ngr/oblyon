<?php
/* Copyright (C) 2015-2022  Open-DSI			<support@open-dsi.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		admin/dashboard.php
 * 	\ingroup	oblyon
 * 	\brief		Dashboard color Page < Oblyon Theme Configurator >
 */

// Dolibarr environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
  $res = @include("../../../main.inc.php"); // From "custom" directory
}

// Libraries
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once '../lib/oblyon.lib.php';


// Translations
$langs->load("admin");
$langs->load("oblyon@oblyon");

// Access control
if (! $user->admin) accessforbidden();

// Parameters OBLYON_*
$dashboard_colors = array (
	'OBLYON_INFOXBOX_WEATHER_COLOR',
	'OBLYON_INFOXBOX_ACTION_COLOR',				// #b46080 AGENDA
	'OBLYON_INFOXBOX_PROJECT_COLOR',			// #6c6a98
	'OBLYON_INFOXBOX_CUSTOMER_PROPAL_COLOR',	// #99a17d PROPAL
	'OBLYON_INFOXBOX_CUSTOMER_ORDER_COLOR',	 	// #99a17d COMMANDE
	'OBLYON_INFOXBOX_CUSTOMER_INVOICE_COLOR',   // #99a17d FACTURE
	'OBLYON_INFOXBOX_SUPPLIER_PROPAL_COLOR',	// #599caf SUPPLIER_PROPOSAL
	'OBLYON_INFOXBOX_SUPPLIER_ORDER_COLOR',	 	// #599caf ORDER_SUPPLIER
	'OBLYON_INFOXBOX_SUPPLIER_INVOICE_COLOR',   // #599caf INVOICE_SUPPLIER
	'OBLYON_INFOXBOX_CONTRAT_COLOR',			// #469686
	'OBLYON_INFOXBOX_BANK_COLOR',				// #c5903e
	'OBLYON_INFOXBOX_ADHERENT_COLOR',			// #79633f
	'OBLYON_INFOXBOX_EXPENSEREPORT_COLOR',		// #79633f
	'OBLYON_INFOXBOX_HOLIDAY_COLOR',			// #755114
	'OBLYON_INFOXBOX_TICKET_COLOR',             // #755114
);


/*
 * Actions
 */
$mesg="";
$action = GETPOST('action', 'alpha');

// set bloc
if ($action == 'set') {
	$value = GETPOST ( 'value', 'int' );
	$name = GETPOST ( 'name', 'text' );

	if ($value == 1) {
		$res = dolibarr_set_const($db, $name, $value, 'yesno', 0, '', $conf->entity);
		if (! $res > 0) $error ++;
	} else {
		$res = dolibarr_set_const($db, $name, $value, 'yesno', 0, '', $conf->entity);
		if (! $res > 0) $error ++;
	}

	if ($error) {
		setEventMessage ( 'Error', 'errors' );
	} else {
		setEventMessage ( $langs->trans ( 'Save' ), 'mesgs' );
	}
}

// set colors
if ($action == 'update') {
	$error = 0;

	foreach ($dashboard_colors as $constname) {
		$constvalue = GETPOST($constname, 'alpha');
		$constvalue = '#'.$constvalue;

		if (! dolibarr_set_const($db, $constname, $constvalue, 'chaine', 0, '', $conf->entity)) {
			$error ++;
		}
	}

	if (empty(GETPOST('THEME_AGRESSIVENESS_RATIO'))) {
		$res = dolibarr_set_const($db, 'THEME_AGRESSIVENESS_RATIO', '-50', 'chaine', 0, '', $conf->entity);
	} else {
		$res = dolibarr_set_const($db, 'THEME_AGRESSIVENESS_RATIO', GETPOST('THEME_AGRESSIVENESS_RATIO'),'chaine',0,'',$conf->entity);
	}

	if (! $res > 0) $error++;

	if (! $error) {
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		setEventMessages($langs->trans("Error"), null, 'errors');
	}

	$_SESSION['dol_resetcache']=dol_print_date(dol_now(),'dayhourlog');
}

/*
 * View
 */
llxHeader('', $langs->trans("OblyonDashboardTitle"),'','','','', array('/oblyon/js/jscolor.js','/oblyon/js/jquery.ui.touch-punch.min.js'),'' );

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'	. $langs->trans("BackToModuleList") . '</a>';
print load_fiche_titre($langs->trans('OblyonDashboardTitle'), $linkback);

// Configuration header
$head = oblyon_admin_prepare_head();

dol_fiche_head($head, 'dashboard', $langs->trans("Module113900Name"), 0, "oblyon@oblyon");

dol_htmloutput_mesg($mesg);

// Setup page goes here

print '<script type="text/javascript">';
print 'r(function(){';
print '	var els = document.getElementsByTagName("link");';
print '	var els_length = els.length;';
print '	for (var i = 0, l = els_length; i < l; i++) {';
print '	var el = els[i];';
print '	   if (el.href.search("style.min.css") >= 0) {';
print '		el.href += "?" + Math.floor(Math.random() * 100);';
print '	}';
print '	}';
print '});';
print 'function r(f){/in/.test(document.readyState)?setTimeout("r("+f+")",9):f()}';
// Colorpicker
print '
$(document).ready(function() {
	var root_font_size = parseInt($("html").css("font-size").split("px")[0]),
	def_dhfs = 1.7 * root_font_size,
	def_shfs = 1.6 * root_font_size,
	def_dvmfs = 1.2 * root_font_size,
	def_svmfs = 1.2 * root_font_size,

	act_rem_dhfs = "' . $act_rem_dhfs . '",
	act_dhfs = parseFloat(act_rem_dhfs.split("rem")[0]) * root_font_size,
	act_px_dhfs = ( act_dhfs.toString() ) + "px",

	act_rem_shfs = "' . $act_rem_shfs . '",
	act_shfs = parseFloat(act_rem_shfs.split("rem")[0]) * root_font_size,
	act_px_shfs = ( act_shfs.toString() ) + "px";

	act_rem_dvmfs = "' . $act_rem_dvmfs . '",
	act_dvmfs = parseFloat(act_rem_dvmfs.split("rem")[0]) * root_font_size,
	act_px_dvmfs = ( act_dvmfs.toString() ) + "px",

	act_rem_svmfs = "' . $act_rem_svmfs . '",
	act_svmfs = parseFloat(act_rem_svmfs.split("rem")[0]) * root_font_size,
	act_px_svmfs = ( act_svmfs.toString() ) + "px";

	$("#dhfs-slider").slider({
		animate: "fast",
		min: -8,
		max: 8,
		step:1
	});
	$("#dhfs-disp-val").html(act_px_dhfs);
	$("#dhfs-stor-val").val(act_rem_dhfs);
	$("#dhfs-slider").slider("value",act_dhfs - def_dhfs);
	$("#dhfs-slider").on("slide",function(event, ui) {
		var dhfs_sel_value = $("#dhfs-slider").slider("value"),
			new_dhfs = def_dhfs + dhfs_sel_value,
			rem_dhfs = (new_dhfs / root_font_size).toString() + "rem";
		$("#dhfs-disp-val").html(new_dhfs.toString() + "px");
		$("#dhfs-stor-val").val(rem_dhfs);
		$("#tmenu_tooltip").css("font-size",rem_dhfs);
		$(".login_block").css("font-size",rem_dhfs);
	});

	$("#shfs-slider").slider({
		animate: "fast",
		min: -8,
		max: 8,
		step:1
	});
	$("#shfs-disp-val").html(act_px_shfs);
	$("#shfs-stor-val").val(act_rem_shfs);
	$("#shfs-slider").slider("value",act_shfs - def_shfs);
	$("#shfs-slider").on("slide",function(event, ui) {
		var shfs_sel_value = $("#shfs-slider").slider("value");
		var new_shfs = def_shfs + shfs_sel_value;
		var rem_shfs = (new_shfs / root_font_size).toString() + "rem";
		$("#shfs-disp-val").html(new_shfs.toString() + "px");
		$("#shfs-stor-val").val(rem_shfs);
	});

	$("#dvmfs-slider").slider({
		animate: "fast",
		min: -8,
		max: 8,
		step:1
	});
	$("#dvmfs-disp-val").html(act_px_dvmfs);
	$("#dvmfs-stor-val").val(act_rem_dvmfs);
	$("#dvmfs-slider").slider("value",act_dvmfs - def_dvmfs);
	$("#dvmfs-slider").on("slide",function(event, ui) {
		var dvmfs_sel_value = $("#dvmfs-slider").slider("value"),
			new_dvmfs = def_dvmfs + dvmfs_sel_value,
			rem_dvmfs = (new_dvmfs / root_font_size).toString() + "rem";
		$("#dvmfs-disp-val").html(new_dvmfs.toString() + "px");
		$("#dvmfs-stor-val").val(rem_dvmfs);
		$("#id-left").css("font-size",rem_dvmfs);
	});

	$("#svmfs-slider").slider({
		animate: "fast",
		min: -8,
		max: 8,
		step:1
	});
	$("#svmfs-disp-val").html(act_px_svmfs);
	$("#svmfs-stor-val").val(act_rem_svmfs);
	$("#svmfs-slider").slider("value",act_svmfs - def_svmfs);
	$("#svmfs-slider").on("slide",function(event, ui) {
		var svmfs_sel_value = $("#svmfs-slider").slider("value");
		var new_svmfs = def_svmfs + svmfs_sel_value;
		var rem_svmfs = (new_svmfs / root_font_size).toString() + "rem";
		$("#svmfs-disp-val").html(new_svmfs.toString() + "px");
		$("#svmfs-stor-val").val(rem_svmfs);
	});
});
';

print '</script>'."\n";

print '<form action="' . $_SERVER["PHP_SELF"] . '" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';

// Colors
print '<table class="noborder as-settings-colors">';

// Infobox enable
print '<tr class="liste_titre">';
print '<td colspan="2">' . $langs->trans('OblyonDashboardDisableBlocks') . '</td>';
print '</tr>' . "\n";

print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableGlobal') . '</td><td>';
print ajax_constantonoff("MAIN_DISABLE_GLOBAL_WORKBOARD", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
print '</td>';
print '</tr>';

// Activation des statistiques globales
print '<tr class="oddeven"><td>' . $langs->trans('DisableGlobalBoxStats') . '</td><td>';
print ajax_constantonoff("MAIN_DISABLE_GLOBAL_BOXSTATS", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
print '</td>';
print '</tr>';

// Invertion des couleurs de fond et d'icone
 print '<tr class="oddeven"><td>' . $langs->trans('InfoboxColorOnBackground') . '</td><td>';
print ajax_constantonoff("THEME_INFOBOX_COLOR_ON_BACKGROUND", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
print '</td>';
print '</tr>';

if ((float) $conf->global->EASYA_VERSION >= 2022.5 || (float) DOL_VERSION >= 15.0) {
	if (empty($conf->global->MAIN_DISABLE_GLOBAL_WORKBOARD)) {
		// Block meteo
		print '<tr class="oddeven"><td>' . $langs->trans('MAIN_DISABLE_METEO') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_METEO", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';

		// Block agenda
		print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableBlockAgenda') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_BLOCK_AGENDA", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';

		// Block projet
		print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableBlockProject') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_BLOCK_PROJECT", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';

		// Block customer
		print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableBlockCustomer') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_BLOCK_CUSTOMER", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';

		// Block supplier
		print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableBlockSupplier') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_BLOCK_SUPPLIER", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';

		// Block contract
		print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableBlockContract') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_BLOCK_CONTRACT", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';

		// Block ticket
		print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableBlockTicket') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_BLOCK_TICKET", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';

		// Block bank
		print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableBlockBank') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_BLOCK_BANK", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';

		// Block adherent
		print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableBlockAdherent') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_BLOCK_ADHERENT", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';

		// Block expense report
		print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableBlockExpenseReport') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_BLOCK_EXPENSEREPORT", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';

		// Block holiday
		print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableBlockHoliday') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_BLOCK_HOLIDAY", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';

		// Block ticket
		print '<tr class="oddeven"><td>' . $langs->trans('DashboardDisableBlockTicket') . '</td><td>';
		print ajax_constantonoff("MAIN_DISABLE_BLOCK_TICKET", array(), $conf->entity, 0, 0, 1, 0, 0, 0, '_red', 'dashboard');
		print '</td>';
		print '</tr>';
	}
}

// Set Intensity
print '<tr class="liste_titre">';
print '<td colspan="2">' . $langs->trans('ColorIntensity') . '</td>';
print '</tr>'."\n";
print '<tr>';
print '<td width="50%">' . $langs->trans('ColorIntensityDesc') . '</td>';
print '<td>';
if (! isset($conf->global->THEME_AGRESSIVENESS_RATIO)) $conf->global->THEME_AGRESSIVENESS_RATIO=-50;
print $langs->trans('ColorMoreDarker') . ' -100 <input type="range" name="THEME_AGRESSIVENESS_RATIO" id="intensity" value="' . $conf->global->THEME_AGRESSIVENESS_RATIO . '" min="-100" max="100"> 100 ' . $langs->trans('ColorMoreLighter');
print '</td></tr>';

// Colors
print '<tr class="liste_titre">';
print '<td colspan="2">' . $langs->trans('Colors') . '</td>';
print '</tr>'."\n";

// Set colors
$num = count($dashboard_colors);
if ($num)
{
	foreach ($dashboard_colors as $key) {
		print '<tr class="value oddeven">';

		// Param
		$label = $langs->trans($key);
		print '<td width="50%">' . $label . '</td>';

		// Value
		print '<td>';
		print '<input type="text" class="color" id="' . $conf->global->$key . '" name="' . $key . '" value="' . $conf->global->$key . '">';
		print '</td></tr>';
	}
}

print '</table>'."\n";

dol_fiche_end();

print '<div class="center">';
print '<input type="submit" class="button" value="' . dol_escape_htmltag($langs->trans('Modify')) . '" name="button">';
print '</div>';

print '</form>';
print '<br>';

// End of page
llxFooter();
$db->close();

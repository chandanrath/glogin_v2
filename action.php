
<?php 
	//echo"pass==".$pass = base64_encode('Devel@p');
?>
  <?PHP
     
$action=$_REQUEST['action'];

/*  pages  */
if($action!="")
{
	 $userData ="SELECT id,code,child,parent FROM "._PREFIX."menu
	WHERE status=1 and  code='".trim($action)."' AND child<>0";
	$resData = mysqli_query($conn,$userData);
	$numRows = mysqli_num_rows($resData);	
	$ChkAction = mysqli_fetch_array($resData);
	
	if($numRows > 0)
	{
	    $ChkAuth = ChkMenuPermission($conn,$_SESSION['user_id'],$ChkAction['id']);
	    $page = $ChkAuth;
		
	}
	else
	{
		$page = "404";
	}
}

//echo"page============================".$page;

/* scripts */

if($action=='logout')
{
	 include_once 'scripts/logout_script.php';
}
else if($action=='')
{	
	if($_SESSION['role']=="admin")
	{
		include_once 'default_admin.php';
	}
	else{
		include_once 'usercheckin.php';	
	}


}
else if($page=='404')
{
	include_once '404.php';	
}
else if($page=='noauth')
{
	include_once 'noauth.php';		
}
else if($page=='yesauth')
{
	if($action=='user_dashboard')	
	{
    	if($_SESSION['role']=="designer" || $_SESSION['role']=="designer-manager")
    	{
    		include_once 'default_designer.php';	
    	}
    	else if($_SESSION['role']=="seo-manager" || $_SESSION['role']=="seo")
    	{
    		include_once 'default_seo.php';	
    	}
    	else if($_SESSION['role']=="dataentry")
    	{
    		include_once 'default_dentry.php';	
    	}
    	else if(($_SESSION['role']=="market") || ($_SESSION['role']=="marketing-manager") || ($_SESSION['role']=="ca-manager") || ($_SESSION['role']=="content-writter") || ($_SESSION['role']=="accounts") || ($_SESSION['role']=="social-media"))
    	{
    		include_once 'default_market.php';	
    	}
		else if($_SESSION['role']=="admin")
		{
			include_once 'default_admin.php';
		}
	}
	if($action=='company')					{  include_once 'pages/company.php'; }
	if($action=='company_crm')					{  include_once 'pages/company_crm.php'; }
	if($action=='company_list')				{  include_once 'pages/company_list.php'; }
	if($action=='company_website')			{  include_once 'pages/company_website.php'; }
	if($action=='company_website_map')		{  include_once 'pages/company_website_map.php'; }
	
	if($action=='company_assign_list')		{  include_once 'pages/company_assign_list.php'; }
	if($action=='company_users')			{  include_once 'pages/company_users.php'; }
	if($action=='company_blank_surl_list')	{  include_once 'pages/company_blank_surl_list.php'; }
	if($action=='completed_website')		{  include_once 'pages/completed_website.php'; }
	if($action=='completed_website_details')		{  include_once 'pages/completed_website_details.php'; }
	
	if($action=='users_list')				{  include_once 'pages/users_list.php'; }
	if($action=='users')					{  include_once 'pages/users.php'; }
	
	if($action=='website_report')			{  include_once 'pages/website_report.php'; }
	
	if($action=='company_upload')			{  include_once 'pages/company_upload.php'; }
	if($action=='add_company')				{  include_once 'scripts/insert_company.php'; }
	
	if($action=='forgot')					{  include_once 'scripts/forgotPasswd.php'; }
	
	
	if($action=='website_list')				{  include_once 'pages/website_list.php'; }
	if($action=='website')					{  include_once 'pages/website.php'; }
	
	if($action=='remove_website')			{  include_once 'pages/removesite.php'; }
	
	if($action=='exporttoexcel')			{  include_once 'scripts/report.php'; }
	if($action=='userreport')				{  include_once 'pages/userreport.php'; }
	
	if($action=='keywords_rank_reports')	{  include_once 'keywordsrank/keywordsrankreports.php'; }
	
	if($action=='verifyotp')				{  include_once 'verifyotp.php'; }
	
	if($action=='contact')					{  include_once 'contact.php'; }
	
	if($action=='cookies_set_users')		{  include_once 'pages/cookies_set_users.php'; }
	
	if($action=='cookies_set_outerusers')		{  include_once 'pages/cookies_set_outerusers.php'; }
	
	if($action=='role_permission')			{  include_once 'pages/role_perm.php'; }
	if($action=='role_menu')				{  include_once 'pages/role_menu.php'; }
	
	if($action=='menu')						{  include_once 'pages/menu.php'; }
	if($action=='menu_add')					{  include_once 'pages/menu_add.php'; }
	
	
	// for cpanel api //
	
	if($action=='cpanel_website_list')		{  include_once 'pages/cpanel/cpanel_website_list.php'; }
	if($action=='cpanel')					{  include_once 'pages/cpanel/cpanel.php'; }
	
	if($action=='domain_add')				{  include_once 'pages/cpanel/domain_add.php'; }
	if($action=='domain_credential')		{  include_once 'pages/cpanel/domain_credential.php'; }
	if($action=='server_add')				{  include_once 'pages/cpanel/server_add.php'; }
	if($action=='server_list')				{  include_once 'pages/cpanel/server_list.php'; }
	if($action=='package_add')				{  include_once 'pages/cpanel/package_add.php'; }
	if($action=='package_list')				{  include_once 'pages/cpanel/package_list.php'; }
	
	if($action=='reseller_update')			{  include_once 'pages/cpanel/reseller_update.php'; }
	if($action=='reseller_register')		{  include_once 'pages/cpanel/reseller_update.php'; }
	
	//if($action=='domain_register')			{  include_once 'pages/cpanel/domain_register.php'; }
//	if($action=='domain_update')			{  include_once 'pages/cpanel/domain_register.php'; }
	
	if($action=='change_passwd')			{  include_once 'pages/cpanel/change_passwd.php'; }
	if($action=='updatedomainpswd')			{  include_once 'pages/cpanel/updatedomainpswd.php'; }	// for cron job //

	if($action=='file_backup')				{  include_once 'pages/cpanel/filebackup.php'; }	// for cron job //
	
	
	if($action=='today_work')				{  include_once 'pages/today_work.php'; }
	if($action=='task_status')				{  include_once 'pages/task_status.php'; }
	if($action=='work_report')				{  include_once 'pages/work_report.php'; }
	
	if($action=='shuffle_script')			{  include_once 'pages/shuffle_script.php'; }
	if($action=='shufflecontent')			{  include_once 'pages/shufflecontent.php'; }
	if($action=='error_log')				{  include_once 'pages/error_log.php'; }
	
	if($action=='add_checklist')			{  include_once 'pages/checklist.php'; }	// add new checklist
	if($action=='checklist_checked')		{  include_once 'pages/checklist_checked.php'; }
	if($action=='domain_checklist')			{  include_once 'pages/domain_checklist.php'; }
	if($action=='checklist_add')			{  include_once 'pages/checklist_add.php'; }
	
	
	if($action=='update_activity')			{  include_once 'pages/update_activity.php'; }
	if($action=='complete_activity')		{  include_once 'pages/complete_activity.php'; }
	
	if($action=='keywords_demo')					{  include_once 'pages/keywords_demo.php'; }
	
	if($action=='html_file_editor')			{  include_once 'pages/htmledit/html_file_editor.php'; }
	if($action=='openhtml')					{  include_once 'pages/htmledit/openhtml.php'; }
	if($action=='openhtml-1')				{  include_once 'pages/htmledit/openhtml-1.php'; }
	if($action=='openhtml-2')				{  include_once 'pages/htmledit/openhtml-2.php'; }
	if($action=='openhtml-3')				{  include_once 'pages/htmledit/openhtml-3.php'; }
	
	if($action=='html_design')				{  include_once 'pages/htmledit/html_design.php'; }
	if($action=='addhtml_design')			{  include_once 'pages/htmledit/addhtml_design.php'; }
	
	if($action=='outdoor_design_list')		{  include_once 'pages/outdoor_design_list.php'; }
	if($action=='outdoor_design')			{  include_once 'pages/outdoor_design.php'; }
	if($action=='outdoor_client_list')		{  include_once 'pages/outdoor_client_list.php'; }
	if($action=='outdoor_client')			{  include_once 'pages/outdoor_client.php'; }
	
	if($action=='changelog')			{  include_once 'pages/cpanel/changelog_backup.php'; }	// 03042020
	
	if($action=='user_checkin')			{  include_once 'pages/usercheckin.php'; }  // 30072020
	
	if($action=='gsuite_setup')			{  include_once 'pages/cpanel/gsuite_setup.php'; }
	
	// for keywords/
	
	if($action=='keywords')					{  include_once 'pages/keywords.php'; }
	if($action=='chemical_list')				{  include_once 'pages/keywords/chemical.php'; }
	if($action=='chemical_composition')			{  include_once 'pages/keywords/chemical_comp.php'; }
	if($action=='addchemical_composition')			{  include_once 'pages/keywords/addchemical_comp.php'; }
	if($action=='mechanical_list')				{  include_once 'pages/keywords/mechanical.php'; }
	if($action=='mechanical_composition')			{  include_once 'pages/keywords/mechanical_comp.php'; }
	if($action=='addmechanical_composition')			{  include_once 'pages/keywords/addmechanical_comp.php'; }
	if($action=='equivalent')				{  include_once 'pages/keywords/equivalent.php'; }
	if($action=='equivalent_grades')			{  include_once 'pages/keywords/equivalent_grades.php'; }
	if($action=='addequivalent_grades')			{  include_once 'pages/keywords/addequivalent_grades.php'; }
	if($action=='astm')							{  include_once 'pages/keywords/astm.php'; }
	if($action=='addastm')							{  include_once 'pages/keywords/addastm.php'; }
	
	
	
	/*******masters********/
	
	if($action=='grades')							{  include_once 'pages/masters/grades.php'; }
	if($action=='addgrades')							{  include_once 'pages/masters/addgrades.php'; }
	if($action=='product')							{  include_once 'pages/masters/products.php'; }
	if($action=='materials')							{  include_once 'pages/masters/material.php'; }
	if($action=='subproduct_type')							{  include_once 'pages/masters/subproduct_type.php'; }
	if($action=='sub_product')							{  include_once 'pages/masters/subproduct.php'; }
	if($action=='background_banner')							{  include_once 'pages/background_banner.php'; }
	
	
	if($action=='keywords_rank')							{  include_once 'keywordsrank/keywords_rank.php'; }
	
	if($action=='multiproduct')					{  include_once 'pages/multiproduct.php'; }
}
else
{
	include_once 'default.php';		
}
?>
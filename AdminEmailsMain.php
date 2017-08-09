<?php

class SpecialAdminEmails extends SpecialPage {

        function __construct() {
                parent::__construct( 'AdminEmails' );
        }

        function execute( $par ) {
                $request = $this->getRequest();
                $output = $this->getOutput();
                $this->setHeaders();


                $dbr = wfGetDB( DB_MASTER );
                $res = $dbr->select(
                        array( 'user', 'user_groups' ),
                        array( 'user_name', 'user_real_name', 'user_email', 'ug_group' ),
                        array( 'ug_group' => 'sysop' ),
                        __METHOD__,
                        array(),
                        array( 'user_groups' => array( 'JOIN', array( 'ug_user=user_id' )))
                );
		
                global $wgSitename;
                $output->addWikiText("'''Admin information for the " . $wgSitename . ":'''");

                $allEmail = '';
                while( $row = $res->fetchRow() ){
                        $allEmail .= $row[user_email] . ';';
                }

                $output->addHTML("<table class='wikitable'><tr><th colspan='3' style='text-align:center;'><a href='mailto:$allEmail' target='_self'>Email All Admins</a></th></tr><tr><th>User Name</th><th>Real Name</th><th>Email</th></tr>");
                foreach( $res->result as $row ) {
                        $output->addHTML("<tr><td>" . Linker::link(Title::makeTitle( 2, $row[user_name] ), $row[user_name]) . "</td><td height='18' padding='0'>");
			if (!empty($row[user_real_name])) {
			    $output->addHTML(Linker::link(Title::makeTitle( 0, $row[user_real_name] ), $row[user_name]));
			}
                        $output->addHTML("</td><td height='18'>");
			if (!empty($row[user_email])) {
			    $output->addHTML("<a href='mailto:$row[user_email]' target='_self'>send email</a>");
			}
			$output->addHTML("</td></tr>");
                }
                $output->addHTML("</table>");
        }
}

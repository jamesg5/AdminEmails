<?php

class SpecialAdminEmails extends SpecialPage {

        function __construct() {
                parent::__construct( 'AdminEmails' );
        }

        function execute( $par ) {
                $request = $this->getRequest();
                $output = $this->getOutput();
		$linkRenderer = $this->getLinkRenderer();
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
		$bodyText = '';
		$bodyText = "<table class='wikitable'><tr><th colspan='3' style='text-align:center;'><a href='mailto:$allEmail' target='_self'>Email All Admins</a></th></tr><tr><th>User Name</th><th>Real Name</th><th>Email</th></tr>";
		foreach( $res->result as $row ) {
			$userNameLink = $linkRenderer->makeLink( new TitleValue( NS_MAIN, User:$row[user_name] ) );
			$bodyText .= "<tr><td>$userNameLink</td><td>";
			if (!empty($row[user_real_name])) {
				$personLink = $linkRenderer->makeLink( new TitleValue( NS_MAIN, $row[user_real_name] ) );
				$bodyText .= "$personLink</td><td>"
			}
			if (!empty($row[user_email])) {
				$bodyText .= "<a href='mailto:$row[user_email]' target='_self'>send email</a>";
			}
			$bodyText .= "</td></tr>";
		}
		$bodyText .= "</table>";
		$output->addHTML($bodyText);
        }
}

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
                        #var_dump($row);
                }

                #$bodyText="{|class='wikitable' \n!colspan='3' |[mailto:$allEmail Email All Admins] \n|- \n!User Name \n!Real Name \n!Email \n";
                $output->addHTML("<table class='wikitable'><tr><th colspan='3' style='text-align:center;'><a href='mailto:$allEmail' target='_self'>Email All Admins</a></th></tr><tr><th>User Name</th><th>Real Name</th><th>Email</th></tr>");
                foreach( $res->result as $row ) {
                        $output->addHTML("<tr><td>");
                        $output->out("[[User:$row[user_name]|$row[user_name]]]");
                        $output->addHTML("</td><td>");
                        $output->out("{{#if:$row[user_real_name] | [[$row[user_real_name]]]| }}");
                        $output->addHTML("</td><td>");
						if (!empty($row[user_email])) {
						    $output->addHTML("<a href='mailto:$row[user_email]' target='_self'>send email</a>");
						}
						$output->addHTML("</td></tr>");
                }
                $output->addHTML("</table>");
        }
}

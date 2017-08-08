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
                $bodyText="{|class='wikitable' \n!colspan='3' |<a href='mailto:$allEmail' target='_self'>Email All Admins</a> \n|- \n!User Name \n!Real Name \n!Email \n";
                foreach( $res->result as $row ) {
                        $bodyText .= " |- \n |[[User:$row[user_name]|$row[user_name]]] \n |{{#if:$row[user_real_name] | [[$row[user_real_name]]]| }} \n |{{#if:$row[user_email] | [mailto:$row[user_email] send email] | }} \n";
                }
                $bodyText .= "|}";
                return array($output->addWikiText($bodyText), 'noparse' => true, 'isHTML' => true );
                #$output->addWikiText($bodyText);
        }
}

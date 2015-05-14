<?php
class Kindle extends Plugin {
    private $host;

    function about() {
        return array(0.2,
                "Share articles to kindle using tinderizer",
                "usr42");
    }

    function init($host) {
        $this->host = $host;

        $host->add_hook($host::HOOK_ARTICLE_BUTTON, $this);
        $host->add_hook($host::HOOK_PREFS_TAB_SECTION, $this);
        $host->add_hook($host::HOOK_HOTKEY_MAP, $this);
        $host->add_hook($host::HOOK_HOTKEY_INFO, $this);
    }

    function hook_hotkey_map($hotkeys) {
        // Use the new target "open_in_background_tab" to define your own
        // hotkey to this function in other plugins.
        $hotkeys['*k'] = 'send_to_kindle';

        return $hotkeys;
    }

    function hook_hotkey_info($hotkeys) {
        $hotkeys[__("Article")]["send_to_kindle"] = __("Send Article to your Kindle");

        return $hotkeys;
    }

    function get_js() {
        return file_get_contents(dirname(__FILE__) . "/kindle.js");
    }

    function hook_article_button($line) {
        return "<img src=\"plugins/kindle/kindle.png\"
                class='tagsPic' style=\"cursor : pointer\"
                onclick=\"emailArticleKindle(".$line["id"].")\"
                alt='Zoom' title='Send to kindle'>";
    }

    function hook_prefs_tab_section($args) {
        if ($args != "prefPrefsAuth") return;

        print "<h2>Kindle ".__('E-mail')."</h2>";

        $email = $this->host->get($this, "kindle-mail");

        print "<form dojoType=\"dijit.form.Form\">";

        print "<script type=\"dojo/method\" event=\"onSubmit\" args=\"evt\">
            evt.preventDefault();
            if (this.validate()) {
                new Ajax.Request('backend.php', {
                    parameters: dojo.objectToQuery(this.getValues()),
                    onComplete: function(transport) {
                        notify_info(transport.responseText);
                    }
                });
            }
        </script>";

        print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"op\" value=\"pluginhandler\">";
        print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"method\" value=\"save\">";
        print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"plugin\" value=\"kindle\">";
        print "<table width=\"100%\" class=\"prefPrefsList\">";
        print "<tr><td width=\"40%\">Kindle ".__('E-mail')."</td>";
        print "<td class=\"prefValue\"><input dojoType=\"dijit.form.ValidationTextBox\" name=\"email\" required=\"1\" value=\"$email\"></td></tr>";
        print "</table>";
        print "<p><button dojoType=\"dijit.form.Button\" type=\"submit\">".__("Save")."</button>";

        print "</form>";
    }

    function save() {
        $email = db_escape_string($_POST["email"]);

        if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            echo "Mail address is not valid!";
            exit;
        }

        $this->host->set($this, "kindle-mail", $email);

        echo __("Configuration saved.");
    }

    function sendEmail() {
        require_once 'classes/ttrssmailer.php';

        $id = db_escape_string($_REQUEST['id']);
        $email = $this->host->get($this, "kindle-mail");

        if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            $reply['error'] =  "No valid mail-address is configured.\nGo to Preferences->'Personal data / Authentication'->'Kindle E-Mail' and save your kindle mail address.";
            print json_encode($reply);
            exit;
        }

        $hex = '';
        $ascii = $email;

        for ($i = 0; $i < strlen($ascii); $i++) {
            $byte = strtolower(dechex(ord($ascii{$i})));
            $byte = str_repeat('0', 2 - strlen($byte)).$byte;
            $hex .= $byte;
        }

        $tindmail = $hex."@tinderizer.com";

        $reply = array();
        $link = "";

        $mail = new ttrssMailer();

        $result = db_query("SELECT email, full_name FROM ttrss_users WHERE id = " . $_SESSION["uid"]);

        $mail->From = strip_tags(htmlspecialchars(db_fetch_result($result, 0, "email")));
        $mail->FromName = strip_tags(htmlspecialchars(db_fetch_result($result, 0, "full_name")));
        $mail->AddAddress($tindmail);

        $result = db_query("SELECT link, content, title, note
                            FROM ttrss_user_entries, ttrss_entries
                            WHERE id = '$id' AND ref_id = id AND owner_uid = " .$_SESSION['uid']);

        $mail->IsHTML(false);

        if (db_num_rows($result) > 1) {
            $subject = __("[Forwarded]") . " " . __("Multiple articles");
        }

        while ($line = db_fetch_assoc($result)) {
            if (!$subject) {
                $subject = __("[Forwarded]") . " " . htmlspecialchars($line["title"]);
            }

            $link = strip_tags($line["link"]);
        }

        $article_link = db_fetch_result($result, 0, 'link');

        $mail->Subject = $subject;
        $mail->Body = strip_tags($article_link);

        $rc = $mail->Send();

        if (!$rc) {
            $reply['error'] =  $mail->ErrorInfo;
        } else {
            $reply['message'] = 'To address: '.$tindmail.'\nLink: '.$article_link;
        }

        print json_encode($reply);
    }

    function api_version() {
        return 2;
    }
}
?>

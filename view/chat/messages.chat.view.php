<?php
	$messageManager = new MessageManager($db);
	$messageList = $messageManager->getAllMessagesBetweenTwoUsers($currentProfile->getId(), $currentUser->getId());
	print_r($messageList);
?>

<script>
setInterval(function() {
	var ajax;

    if (window.XMLHttpRequest) {
        ajax = new XMLHttpRequest();
    } else {
        alert("Il semble que votre navigateur ne supporte pas AJAX. :(");
        return false;
    }

    var data = {};
    data["info"] = "messagesBetweenTwoUsers";
    data["fromUser"] = <?php echo $currentProfile->getId();?>;
    data["toUser"] = <?php echo $currentUser->getId();?>;
    if (isNaN(data["lastCallTime"])) {
    	data["lastCallTime"] = 0;
    }

    var formData = new FormData();

    formData.append('action', "getNewMessagesBetweenTwoUsers");
    formData.append('data', JSON.stringify(data));
    ajax.open('post', "action.php", true);
    ajax.send(formData);

    ajax.onreadystatechange = function() {
        if (ajax.readyState == 4 && ajax.status == 200) {
            var reply = ajax.responseText;
            var toShow = JSON.parse(reply);

            if (toShow['output'] == "ok") {
                
            } else {
                alert(toShow["err_msg"]);
            }
        }
    }
    return ajax;
}, 5000)

function sendMessage() {
	var ajax;

    if (window.XMLHttpRequest) {
        ajax = new XMLHttpRequest();
    }
    else {
        alert("Il semble que votre navigateur ne supporte pas AJAX. :(");
        return false;
    }

    ajax.onreadystatechange = function() {
        if (ajax.readyState == 4 && ajax.status == 200) {
        	var reply = ajax.responseText
            console.log(reply);
        }
    }

    var data = {};
    data['message'] = document.getElementById("SendMessageTextBox").value;
    data['toUser'] = <?php echo $currentProfile->getId();?>;

    var formData = new FormData();

    formData.append('action', "sendMessage");
    formData.append('data', JSON.stringify(data));
    ajax.open('post', "action.php", true);
    ajax.send(formData);

    return ajax;
}

</script>

<div id="sendMessage">
<input type="text" name="message" id="SendMessageTextBox" placeholder="Tapez votre message ici"><button type="button" id="SendMessageButton" onClick="sendMessage();">Envoyer le message</button><br>
</div>

<div id="messageBox">
<?php
if (empty($messageList)) {
	echo "<p>Vous n'avez pas encore echange de message.</p>";
} else {
	echo "<div id=\"messageList\">";
	foreach($messageList as $message) {
		$symbol = ($message->getToUser() == $currentUser->getId()) ? "->" : "<-";
		echo "<div class=\"msgInList\" id=\"msg" . $message->getId() . "\"><p>" . $symbol . " " . $message->getContent() . "</p></div>";
	}
}
?>
</div>
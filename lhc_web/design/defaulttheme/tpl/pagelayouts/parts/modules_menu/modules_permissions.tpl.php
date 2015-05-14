<?php 
	$useQuestionary = $currentUser->hasAccessTo('lhquestionary','manage_questionary');
	$useFaq = $currentUser->hasAccessTo('lhfaq','manage_faq');
	$useChatbox = $currentUser->hasAccessTo('lhchatbox','manage_chatbox');
	$useBo = $currentUser->hasAccessTo('lhbrowseoffer','manage_bo');
	$useFm = $currentUser->hasAccessTo('lhform','manage_fm');
?>
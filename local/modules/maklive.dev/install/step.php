<?
echo CAdminMessage::ShowNote(GetMessage("MODULE_INSTALLED_SUCCESS"));
LocalRedirect($APPLICATION->GetCurUri()."&result=DELOK");
<?php
namespace App\Eventbuizz\EBObject;

class EBUtility
{
    private $_data;
    protected $_event_id;
    protected $_language_id;
    protected $_organizer_id;
    protected $_panel;
    protected $_draft;
    protected $saleAgentId;

    public function __construct($data, $registration_panel, $organizer_id, $event_id = false, $language_id = false, $draft = false, $saleAgentId = 0)
    {
        $this->_data = $data;
        $this->_setPanel($registration_panel);
        $this->_setOrganizerId($organizer_id);
        $this->_setEventId($event_id);
        $this->_setLanguageId($language_id);
        $this->_setDraft($draft);
        $this->_setSaleAgendID($saleAgentId);
    }

    private function _setPanel($registration_panel)
    {
        //Ex => ["admin", "sale", "reporting", "attendee"]
        $this->_panel = $registration_panel;
    }

    private function _setDraft($draft)
    {
        $this->_draft = $draft;
    }

    private function _setOrganizerId($organizer_id)
    {
        $this->_organizer_id = $organizer_id;
    }

    private function _setSaleAgendID($saleAgentId)
    {
        $this->saleAgentId = $saleAgentId;
    }

    private function _setEventId($event_id)
    {
        $this->_event_id = $event_id;
    }

    public function getEventId()
    {
        return $this->_event_id;
    }

    public function getData($key = null)
    {
        return ($key != '') ? $this->_data[$key] : $this->_data;
    }

    public function getPanel()
    {
        return $this->_panel;
    }

    private function _setLanguageId($language_id)
    {
        $this->_language_id = $language_id;
    }

    public function getLangaugeId()
    {
        return $this->_language_id;
    }

    public function getSalesAgentID()
    {
        return $this->saleAgentId;
    }

    public function getOrganizerID()
    {
        return $this->_organizer_id;
    }

    public function getSessionID()
    {
        // need to update according eventbuizz-api
        return 1;
    }

    public function getAllData()
    {
        return $this->_data;
    }

    public function isDraft()
    {
        return $this->_draft == true;
    }
}

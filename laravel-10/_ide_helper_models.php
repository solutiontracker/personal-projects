<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\ActivityLog
 *
 * @property int $id
 * @property string $description
 * @property int|null $admin_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|ActivityLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ActivityLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ActivityLog withoutTrashed()
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ActorActivityLog
 *
 * @property int $id
 * @property string $module_alias
 * @property int $actor_id
 * @property string $actor_type
 * @property string $action
 * @property string $activity
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|ActorActivityLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog whereActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog whereActorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog whereModuleAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActorActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ActorActivityLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ActorActivityLog withoutTrashed()
 */
	class ActorActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AddAttendeeLog
 *
 * @property int $id
 * @property int $attendee_id
 * @property int $event_id
 * @property int $organizer_id
 * @property string $type
 * @property int $status 0=pending;1=finsihed; 2= failed
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Attendee $attendee
 * @property-read \App\Models\Event $event
 * @property-read \App\Models\Organizer $organizer
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|AddAttendeeLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddAttendeeLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AddAttendeeLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AddAttendeeLog withoutTrashed()
 */
	class AddAttendeeLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AdditionalInfoMenu
 *
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property int $sort_order
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AdditionalInfoMenuInfo[] $Info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu newQuery()
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoMenu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoMenu withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoMenu withoutTrashed()
 */
	class AdditionalInfoMenu extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AdditionalInfoMenuInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $menu_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoMenuInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoMenuInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoMenuInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoMenuInfo withoutTrashed()
 */
	class AdditionalInfoMenuInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AdditionalInfoPage
 *
 * @property int $id
 * @property int $sort_order
 * @property int $menu_id
 * @property int $event_id
 * @property int $page_type 1=cms page; 2=url
 * @property string $image
 * @property string $image_position
 * @property string $pdf
 * @property string $icon
 * @property string $url
 * @property string $website_protocol
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AdditionalInfoPageInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage newQuery()
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoPage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereImagePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage wherePageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage wherePdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPage whereWebsiteProtocol($value)
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoPage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoPage withoutTrashed()
 */
	class AdditionalInfoPage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AdditionalInfoPageInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $page_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoPageInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalInfoPageInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoPageInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AdditionalInfoPageInfo withoutTrashed()
 */
	class AdditionalInfoPageInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Addon
 *
 * @property int $id
 * @property int $admin_id
 * @property string $name
 * @property string $alias
 * @property string $description
 * @property string $basic_addons 1=basic, 0=none
 * @property int $module_id
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Addon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Addon newQuery()
 * @method static \Illuminate\Database\Query\Builder|Addon onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Addon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereBasicAddons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Addon withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Addon withoutTrashed()
 */
	class Addon extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AdminActivityLog
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $ip
 * @property string|null $browser
 * @property string|null $os
 * @property string|null $history_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|AdminActivityLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog whereHistoryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog whereOs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminActivityLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|AdminActivityLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AdminActivityLog withoutTrashed()
 */
	class AdminActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Administrator
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $status
 * @property string $type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator newQuery()
 * @method static \Illuminate\Database\Query\Builder|Administrator onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator query()
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Administrator withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Administrator withoutTrashed()
 */
	class Administrator extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Agenda
 *
 * @property int $id
 * @property int $event_id
 * @property string $start_date
 * @property string $start_time
 * @property string $link_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $workshop_id
 * @property int $qa
 * @property int $ticket
 * @property int $enable_checkin
 * @property int $enable_speakerlist
 * @property int $hide_on_registrationsite
 * @property int|null $hide_on_app
 * @property int|null $only_for_qa
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventAgendaSpeaker[] $attendee_assign
 * @property-read int|null $attendee_assign_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventGroup[] $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgendaInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventTrack[] $tracks
 * @property-read int|null $tracks_count
 * @property-read \App\Models\AgendaVideo|null $video
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgendaVideo[] $videos
 * @property-read int|null $videos_count
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda newQuery()
 * @method static \Illuminate\Database\Query\Builder|Agenda onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda query()
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereEnableCheckin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereEnableSpeakerlist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereHideOnApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereHideOnRegistrationsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereOnlyForQa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereQa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereTicket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agenda whereWorkshopId($value)
 * @method static \Illuminate\Database\Query\Builder|Agenda withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Agenda withoutTrashed()
 */
	class Agenda extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AgendaInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $agenda_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaInfo whereValue($value)
 */
	class AgendaInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AgendaNote
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $agenda_id
 * @property string $notes
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote newQuery()
 * @method static \Illuminate\Database\Query\Builder|AgendaNote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AgendaNote withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AgendaNote withoutTrashed()
 */
	class AgendaNote extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AgendaSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $agenda_list 0=time,1=track
 * @property int $session_ratings
 * @property int $agenda_tab
 * @property int $admin_fav_attendee
 * @property int $attach_attendee_mobile
 * @property int $qa
 * @property int $program_fav
 * @property int $show_tracks
 * @property int $show_attach_attendee
 * @property int $agenda_display_time
 * @property int $show_program_dashboard
 * @property int $show_my_program_dashboard
 * @property int $agenda_collapse_workshop
 * @property int $agendaTimer
 * @property int $agenda_search_filter
 * @property int $agenda_display_alerts
 * @property int $enable_notes
 * @property int|null $enable_program_attendee
 * @property int|null $program_groups
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereAdminFavAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereAgendaCollapseWorkshop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereAgendaDisplayAlerts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereAgendaDisplayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereAgendaList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereAgendaSearchFilter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereAgendaTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereAgendaTimer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereAttachAttendeeMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereEnableNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereEnableProgramAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereProgramFav($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereProgramGroups($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereQa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereSessionRatings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereShowAttachAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereShowMyProgramDashboard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereShowProgramDashboard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereShowTracks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSetting whereUpdatedAt($value)
 */
	class AgendaSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AgendaSpeakerlistRequest
 *
 * @property int $id
 * @property int $event_id
 * @property int $agenda_id
 * @property int $attendee_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSpeakerlistRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSpeakerlistRequest newQuery()
 * @method static \Illuminate\Database\Query\Builder|AgendaSpeakerlistRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSpeakerlistRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSpeakerlistRequest whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSpeakerlistRequest whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSpeakerlistRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSpeakerlistRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSpeakerlistRequest whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSpeakerlistRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSpeakerlistRequest whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaSpeakerlistRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AgendaSpeakerlistRequest withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AgendaSpeakerlistRequest withoutTrashed()
 */
	class AgendaSpeakerlistRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AgendaVideo
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $type
 * @property string|null $plateform
 * @property string|null $size
 * @property string|null $url
 * @property string|null $filename
 * @property int|null $agenda_id
 * @property int $status
 * @property int $is_live
 * @property string|null $thumbnail
 * @property int|null $is_iframe
 * @property string|null $iframe_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $sort
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo newQuery()
 * @method static \Illuminate\Database\Query\Builder|AgendaVideo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereIframeData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereIsIframe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereIsLive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo wherePlateform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgendaVideo whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|AgendaVideo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AgendaVideo withoutTrashed()
 */
	class AgendaVideo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AssignPackage
 *
 * @property int $id
 * @property int $admin_id
 * @property int $organizer_id
 * @property int $package_id
 * @property string $no_of_event
 * @property int $expire_duration
 * @property int|null $total_attendees
 * @property string $package_assign_date
 * @property string $package_expire_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Addon[] $assignPackageAddons
 * @property-read int|null $assign_package_addons_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AssignPackageUsed[] $packageUsed
 * @property-read int|null $package_used_count
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage whereExpireDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage whereNoOfEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage wherePackageAssignDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage wherePackageExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage whereTotalAttendees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackage whereUpdatedAt($value)
 */
	class AssignPackage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AssignPackageAddon
 *
 * @property int $id
 * @property int $assign_package_id
 * @property int $addons_id
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageAddon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageAddon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageAddon query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageAddon whereAddonsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageAddon whereAssignPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageAddon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageAddon whereType($value)
 */
	class AssignPackageAddon extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AssignPackageUsed
 *
 * @property int $id
 * @property int $assign_package_id
 * @property int $event_id
 * @property string $is_expire
 * @property string $event_create_date
 * @property string $event_expire_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed whereAssignPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed whereEventCreateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed whereEventExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed whereIsExpire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignPackageUsed whereUpdatedAt($value)
 */
	class AssignPackageUsed extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Attendee
 *
 * @property int $id
 * @property string $email
 * @property string $ss_number
 * @property string $password
 * @property string $first_name
 * @property string|null $last_name
 * @property int $organizer_id
 * @property string|null $FIRST_NAME_PASSPORT
 * @property string|null $LAST_NAME_PASSPORT
 * @property string|null $BIRTHDAY_YEAR
 * @property string|null $EMPLOYMENT_DATE
 * @property string|null $SPOKEN_LANGUAGE
 * @property \App\Models\EventAttendeeImage|null $image
 * @property int $status
 * @property string $show_home
 * @property int $allow_vote 1=Yes, 0=No
 * @property int $billing_ref_attendee
 * @property string $billing_password
 * @property int $change_password
 * @property string|null $phone
 * @property int $is_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventGroup[] $adminEventGroups
 * @property-read int|null $admin_event_groups_count
 * @property-read \App\Models\AttendeeBilling|null $billing
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventAgendaSpeaker[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \App\Models\EventAttendee|null $event
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventGroup[] $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AttendeeInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|Attendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereAllowVote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereBIRTHDAYYEAR($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereBillingPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereBillingRefAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereChangePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereEMPLOYMENTDATE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereFIRSTNAMEPASSPORT($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereIsUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereLASTNAMEPASSPORT($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereSPOKENLANGUAGE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereShowHome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereSsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Attendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Attendee withoutTrashed()
 */
	class Attendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeActivityLog
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property string|null $ip
 * @property string|null $browser
 * @property string|null $os
 * @property string|null $platform
 * @property string|null $history_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeActivityLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog whereHistoryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog whereOs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeActivityLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeActivityLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeActivityLog withoutTrashed()
 */
	class AttendeeActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeAuthentication
 *
 * @property int $id
 * @property string|null $email
 * @property string|null $token
 * @property string|null $type
 * @property string|null $to
 * @property string|null $refrer
 * @property int|null $event_id
 * @property string|null $expire_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeAuthentication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication whereRefrer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeAuthentication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeAuthentication withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeAuthentication withoutTrashed()
 */
	class AttendeeAuthentication extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeBilling
 *
 * @property int $id
 * @property int $order_id
 * @property int $organizer_id
 * @property int $event_id
 * @property int $attendee_id
 * @property int|null $billing_membership
 * @property string|null $billing_member_number
 * @property string|null $billing_private_street
 * @property string|null $billing_private_house_number
 * @property string|null $billing_private_post_code
 * @property string|null $billing_private_city
 * @property string|null $billing_private_country
 * @property string|null $billing_company_type
 * @property string|null $billing_company_registration_number
 * @property string|null $billing_ean
 * @property string|null $billing_contact_person_name
 * @property string|null $billing_contact_person_email
 * @property string|null $billing_contact_person_mobile_number
 * @property string|null $billing_company_street
 * @property string|null $billing_company_house_number
 * @property string|null $billing_company_post_code
 * @property string|null $billing_company_city
 * @property string|null $billing_company_country
 * @property string|null $billing_poNumber
 * @property string|null $invoice_reference_no
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $is_updated_order
 * @property-read \App\Models\Country|null $country
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeBilling onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingCompanyCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingCompanyCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingCompanyHouseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingCompanyPostCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingCompanyRegistrationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingCompanyStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingCompanyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingContactPersonEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingContactPersonMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingContactPersonName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingEan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingMemberNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingMembership($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingPoNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingPrivateCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingPrivateCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingPrivateHouseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingPrivatePostCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereBillingPrivateStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereInvoiceReferenceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereIsUpdatedOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBilling whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeBilling withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeBilling withoutTrashed()
 */
	class AttendeeBilling extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeBillingItemEmail
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property string $attendee_success_email
 * @property string $invite_email
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBillingItemEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBillingItemEmail newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeBillingItemEmail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBillingItemEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBillingItemEmail whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBillingItemEmail whereAttendeeSuccessEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBillingItemEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBillingItemEmail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBillingItemEmail whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBillingItemEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBillingItemEmail whereInviteEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeBillingItemEmail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeBillingItemEmail withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeBillingItemEmail withoutTrashed()
 */
	class AttendeeBillingItemEmail extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeChangeLog
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property string $attribute_name
 * @property string|null $old_value
 * @property string|null $new_value
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeChangeLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog whereAttributeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog whereNewValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog whereOldValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeChangeLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeChangeLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeChangeLog withoutTrashed()
 */
	class AttendeeChangeLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeDeletionLog
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $order_id
 * @property int $additional_attendee
 * @property string $date
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeDeletionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeDeletionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeDeletionLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeDeletionLog whereAdditionalAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeDeletionLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeDeletionLog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeDeletionLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeDeletionLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeDeletionLog whereOrderId($value)
 */
	class AttendeeDeletionLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeExportEmailTemplate
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $template
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeExportEmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeExportEmailTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeExportEmailTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeExportEmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeExportEmailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeExportEmailTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeExportEmailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeExportEmailTemplate whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeExportEmailTemplate whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeExportEmailTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeExportEmailTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeExportEmailTemplate withoutTrashed()
 */
	class AttendeeExportEmailTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeFieldSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $initial
 * @property int $first_name
 * @property int $last_name
 * @property int $email
 * @property int $password
 * @property int $phone_number
 * @property int $age
 * @property int $gender
 * @property int $first_name_passport
 * @property int $last_name_passport
 * @property int $place_of_birth
 * @property int $passport_no
 * @property int $date_of_issue_passport
 * @property int $date_of_expiry_passport
 * @property int $birth_date
 * @property int $spoken_languages
 * @property int $profile_picture
 * @property int $website
 * @property int $linkedin
 * @property int $facebook
 * @property int $twitter
 * @property int $company_name
 * @property int $title
 * @property int $department
 * @property int $organization
 * @property int $employment_date
 * @property int $custom_field
 * @property int $country
 * @property int $industry
 * @property int $job_tasks
 * @property int $interests
 * @property int $about
 * @property int $network_group
 * @property int $delegate_number
 * @property int $table_number
 * @property int $event_language
 * @property int $pa_house_no
 * @property int $pa_street
 * @property int $pa_post_code
 * @property int $pa_city
 * @property int $pa_country
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereCustomField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereDateOfExpiryPassport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereDateOfIssuePassport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereDelegateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereEmploymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereEventLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereFirstNamePassport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereInterests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereJobTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereLastNamePassport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereNetworkGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting wherePaCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting wherePaCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting wherePaHouseNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting wherePaPostCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting wherePaStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting wherePassportNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting wherePlaceOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereProfilePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereSpokenLanguages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereTableNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeFieldSetting whereWebsite($value)
 */
	class AttendeeFieldSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeInfo
 *
 * @property int $id
 * @property string $name
 * @property string|null $value
 * @property int $attendee_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeInfo withoutTrashed()
 */
	class AttendeeInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeInvite
 *
 * @property int $id
 * @property int $event_id
 * @property int $organizer_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property int $status 0=not-sent,1=sent,2=reminder-sent
 * @property int $sms_sent
 * @property int $not_send
 * @property int $is_attending
 * @property int $is_resend
 * @property string $date_sent
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeInvite onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereDateSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereIsAttending($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereIsResend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereNotSend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereSmsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInvite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeInvite withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeInvite withoutTrashed()
 */
	class AttendeeInvite extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeInviteLog
 *
 * @property int $id
 * @property int $event_id
 * @property int $organizer_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property int $email_sent
 * @property int $sms_sent
 * @property int $not_sent
 * @property string $date_sent
 * @property string $type
 * @property int $status
 * @property string $status_msg
 * @property string $sms
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeInviteLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereDateSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereEmailSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereNotSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereSmsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereStatusMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeInviteLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeInviteLog withoutTrashed()
 */
	class AttendeeInviteLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeInviteStats
 *
 * @property int $id
 * @property int|null $organizer_id
 * @property int|null $event_id
 * @property string|null $template_alias
 * @property int|null $open
 * @property int|null $click
 * @property int|null $reject
 * @property int|null $send
 * @property int|null $deferral
 * @property int|null $hard_bounce
 * @property int|null $soft_bounce
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeInviteStats onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereClick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereDeferral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereHardBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereReject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereSend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereSoftBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereTemplateAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeInviteStats whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeInviteStats withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeInviteStats withoutTrashed()
 */
	class AttendeeInviteStats extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeLabel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeLabel query()
 */
	class AttendeeLabel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeMatchKeyword
 *
 * @property int $id
 * @property int $organizer_id
 * @property int $attendee_id
 * @property int $event_id
 * @property int $keyword_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeMatchKeyword onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword whereKeywordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeMatchKeyword whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeMatchKeyword withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeMatchKeyword withoutTrashed()
 */
	class AttendeeMatchKeyword extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeRegistrationLog
 *
 * @property int $id
 * @property int $organizer_id
 * @property int $event_id
 * @property int $attendee_id
 * @property string $reg_date
 * @property string $cancel_date
 * @property string $status
 * @property string $comments
 * @property string $register_by
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeRegistrationLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog whereCancelDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog whereRegDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog whereRegisterBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeRegistrationLog whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeRegistrationLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeRegistrationLog withoutTrashed()
 */
	class AttendeeRegistrationLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendeeSetting
 *
 * @property int $id
 * @property string|null $domain_names
 * @property int $event_id
 * @property int $phone
 * @property int $email
 * @property int $title
 * @property int $organization
 * @property int $department
 * @property int $company_name
 * @property int $show_country
 * @property int $contact_vcf
 * @property int $linkedin
 * @property int $linkedin_registration
 * @property int $registration_password
 * @property int $program
 * @property int $attendee_group
 * @property int|null $attendee_my_group
 * @property int $tab
 * @property int $initial
 * @property int $network_group
 * @property int $table_number
 * @property int $delegate_number
 * @property int $voting
 * @property int $allow_my_document
 * @property int $image_gallery
 * @property string $default_display
 * @property int $create_profile
 * @property string $default_password
 * @property int $facebook_enable
 * @property int $hide_password
 * @property int $default_password_label
 * @property int $forgot_link
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $attendee_reg_verification
 * @property int $validate_attendee_invite
 * @property int $interest
 * @property int $show_custom_field
 * @property int $bio_info
 * @property int $show_job_tasks
 * @property int $show_industry
 * @property int $password_lenght
 * @property int $strong_password
 * @property int $enable_foods
 * @property int $authentication
 * @property int $change_password_2fa
 * @property int $place_of_birth
 * @property int $passport_no
 * @property int $date_of_issue_passport
 * @property int $date_of_expiry_passport
 * @property int $pa_house_no
 * @property int $pa_street
 * @property int $pa_post_code
 * @property int $pa_city
 * @property int $pa_country
 * @property int $display_private_address
 * @property int|null $crp
 * @property int|null $cpr
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|AttendeeSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereAllowMyDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereAttendeeGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereAttendeeMyGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereAttendeeRegVerification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereAuthentication($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereBioInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereChangePassword2fa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereContactVcf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereCpr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereCreateProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereCrp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereDateOfExpiryPassport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereDateOfIssuePassport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereDefaultDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereDefaultPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereDefaultPasswordLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereDelegateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereDisplayPrivateAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereDomainNames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereEnableFoods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereFacebookEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereForgotLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereHidePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereImageGallery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereInterest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereLinkedinRegistration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereNetworkGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting wherePaCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting wherePaCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting wherePaHouseNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting wherePaPostCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting wherePaStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting wherePassportNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting wherePasswordLenght($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting wherePlaceOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereRegistrationPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereShowCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereShowCustomField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereShowIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereShowJobTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereStrongPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereTableNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereValidateAttendeeInvite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendeeSetting whereVoting($value)
 * @method static \Illuminate\Database\Query\Builder|AttendeeSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AttendeeSetting withoutTrashed()
 */
	class AttendeeSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AutologinTokens
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string|null $path
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AutologinTokens newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AutologinTokens newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AutologinTokens query()
 * @method static \Illuminate\Database\Eloquent\Builder|AutologinTokens whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutologinTokens whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutologinTokens whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutologinTokens wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutologinTokens whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutologinTokens whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutologinTokens whereUserId($value)
 */
	class AutologinTokens extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BadgePrintHistory
 *
 * @property int $id
 * @property int $event_id
 * @property int $badge_id
 * @property string $badge_for
 * @property string $badge_type
 * @property string $print_date
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrintHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrintHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrintHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrintHistory whereBadgeFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrintHistory whereBadgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrintHistory whereBadgeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrintHistory whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrintHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrintHistory wherePrintDate($value)
 */
	class BadgePrintHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BadgePrinterPort
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrinterPort newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrinterPort newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BadgePrinterPort query()
 */
	class BadgePrinterPort extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingField
 *
 * @property int $id
 * @property int $sort_order
 * @property int $event_id
 * @property int $status
 * @property int $mandatory
 * @property string $field_alias
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $type
 * @property string $section_alias
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillingFieldInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField whereFieldAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField whereMandatory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField whereSectionAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|BillingField withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingField withoutTrashed()
 */
	class BillingField extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingFieldInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $field_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingFieldInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingFieldInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingFieldInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingFieldInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingFieldInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingFieldInfo whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingFieldInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingFieldInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingFieldInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingFieldInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingFieldInfo whereValue($value)
 */
	class BillingFieldInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingHotelSession
 *
 * @property int $id
 * @property int $event_id
 * @property int $hotel_id
 * @property int $rooms
 * @property int $room_id
 * @property string $date_reserved
 * @property string $session_id
 * @property int $is_release
 * @property int $edit_order_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingHotelSession onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereDateReserved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereEditOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereIsRelease($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingHotelSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|BillingHotelSession withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingHotelSession withoutTrashed()
 */
	class BillingHotelSession extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingItem
 *
 * @property int $id
 * @property int $group_id
 * @property string $group_type
 * @property string $group_required
 * @property string $group_is_expanded
 * @property string $link_to
 * @property string $link_to_id
 * @property int $sort_order
 * @property string $item_number
 * @property int $event_id
 * @property int $organizer_id
 * @property float $price
 * @property float $vat
 * @property int $qty
 * @property int $total_tickets
 * @property int $status
 * @property int|null $ticket_item_id
 * @property int $is_free
 * @property int $is_default
 * @property int $is_required
 * @property string $type
 * @property int $is_internal
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $is_archive
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $event_items
 * @property-read int|null $event_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillingItemInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillingItemRule[] $rules
 * @property-read int|null $rules_count
 * @property-read \Illuminate\Database\Eloquent\Collection|BillingItem[] $subitem
 * @property-read int|null $subitem_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillingOrderAddon[] $used_items
 * @property-read int|null $used_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem validItem()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereGroupIsExpanded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereGroupRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereGroupType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereIsArchive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereIsFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereIsInternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereItemNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereLinkTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereLinkToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereTicketItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereTotalTickets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItem whereVat($value)
 * @method static \Illuminate\Database\Query\Builder|BillingItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingItem withoutTrashed()
 */
	class BillingItem extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingItemEvent
 *
 * @property int $id
 * @property int $item_id
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemEvent newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingItemEvent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemEvent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemEvent whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemEvent whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemEvent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|BillingItemEvent withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingItemEvent withoutTrashed()
 */
	class BillingItemEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingItemGroup
 *
 * @property int $id
 * @property int $event_id
 * @property int $organizer_id
 * @property int $status
 * @property string $group_type
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingItemGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup whereGroupType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|BillingItemGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingItemGroup withoutTrashed()
 */
	class BillingItemGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingItemGroupInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $group_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroupInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroupInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingItemGroupInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroupInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroupInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroupInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroupInfo whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroupInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroupInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroupInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroupInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemGroupInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|BillingItemGroupInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingItemGroupInfo withoutTrashed()
 */
	class BillingItemGroupInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingItemInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $item_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingItemInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemInfo whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|BillingItemInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingItemInfo withoutTrashed()
 */
	class BillingItemInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingItemRule
 *
 * @property int $id
 * @property int $item_id
 * @property string $rule_type
 * @property string $discount_type
 * @property float $discount
 * @property float $price
 * @property string $start_date
 * @property string $end_date
 * @property int $qty
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillingItemRuleInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingItemRule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereRuleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|BillingItemRule withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingItemRule withoutTrashed()
 */
	class BillingItemRule extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingItemRuleInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $rule_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo whereRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingItemRuleInfo whereValue($value)
 */
	class BillingItemRuleInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingOrder
 *
 * @property int $id
 * @property int $event_id
 * @property int $parent_id
 * @property int $language_id
 * @property int $attendee_id
 * @property int $sale_agent_id
 * @property int $sale_type
 * @property string $session_id
 * @property float $event_price
 * @property int $event_qty
 * @property float $event_discount
 * @property string $security
 * @property float|null $vat
 * @property float|null $vat_amount
 * @property float|null $payment_fee
 * @property float|null $payment_fee_vat
 * @property string $transaction_id
 * @property string|null $invoice_reference_no
 * @property float $grand_total
 * @property float $reporting_panel_total
 * @property float $corrected_total
 * @property float $summary_sub_total
 * @property int $total_attendee
 * @property string $discount_type
 * @property string|null $code
 * @property int $coupon_id
 * @property float $discount_amount
 * @property float|null $quantity_discount
 * @property string $order_date
 * @property int $eventsite_currency
 * @property int $order_number
 * @property int $billing_quantity
 * @property string $status
 * @property int $is_cancelled_wcn
 * @property string|null $comments
 * @property int $is_voucher
 * @property int $is_payment_received
 * @property string|null $payment_received_date
 * @property string $order_type
 * @property string $dibs_dump
 * @property int $is_free
 * @property int $is_waitinglist
 * @property int $is_tango
 * @property int $e_invoice
 * @property int $is_archive
 * @property string|null $user_agent
 * @property string|null $session_data
 * @property int $is_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $hide_first_billing_item_description
 * @property int $is_added_reporting
 * @property int $to_be_fetched
 * @property int|null $new_imp_flag
 * @property int $is_updated_qty
 * @property int $item_level_vat
 * @property-read \Illuminate\Database\Eloquent\Collection|BillingOrder[] $child_orders
 * @property-read int|null $child_orders_count
 * @property-read BillingOrder|null $latestChild
 * @property-read BillingOrder|null $latestSibling
 * @property-read \App\Models\Attendee $order_attendee
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillingOrderAttendee[] $order_attendees
 * @property-read int|null $order_attendees_count
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder currentOrder()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereBillingQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereCorrectedTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereDibsDump($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereEInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereEventDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereEventPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereEventQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereEventsiteCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereGrandTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereHideFirstBillingItemDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereInvoiceReferenceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereIsAddedReporting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereIsArchive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereIsCancelledWcn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereIsFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereIsPaymentReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereIsTango($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereIsUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereIsUpdatedQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereIsVoucher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereIsWaitinglist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereItemLevelVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereNewImpFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereOrderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder wherePaymentFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder wherePaymentFeeVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder wherePaymentReceivedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereQuantityDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereReportingPanelTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereSaleAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereSaleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereSecurity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereSessionData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereSummarySubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereToBeFetched($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereTotalAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrder whereVatAmount($value)
 * @method static \Illuminate\Database\Query\Builder|BillingOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingOrder withoutTrashed()
 */
	class BillingOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingOrderAddon
 *
 * @property int $id
 * @property int $order_id
 * @property int $attendee_id
 * @property int $addon_id
 * @property string $name
 * @property float $price
 * @property float $vat
 * @property int $qty
 * @property float $discount
 * @property int $discount_qty
 * @property int $discount_type
 * @property int|null $ticket_item_id
 * @property int $parent
 * @property string $link_to
 * @property string $link_to_id
 * @property int $group_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderAddon onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereAddonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereDiscountQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereLinkTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereLinkToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereTicketItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddon whereVat($value)
 * @method static \Illuminate\Database\Query\Builder|BillingOrderAddon withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderAddon withoutTrashed()
 */
	class BillingOrderAddon extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingOrderAddonCreditNote
 *
 * @property int $id
 * @property int $order_id
 * @property int $credit_note_id
 * @property int $order_number
 * @property int $attendee_id
 * @property int $addon_id
 * @property string $name
 * @property float $price
 * @property float $vat
 * @property int $qty
 * @property float $discount
 * @property int $parent
 * @property string $link_to
 * @property string $link_to_id
 * @property int $group_id
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereAddonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereCreditNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereLinkTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereLinkToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAddonCreditNote whereVat($value)
 */
	class BillingOrderAddonCreditNote extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingOrderAttendee
 *
 * @property int $id
 * @property int $order_id
 * @property int $attendee_id
 * @property int $event_qty
 * @property float $event_discount
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderAttendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendee whereEventDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendee whereEventQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendee whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|BillingOrderAttendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderAttendee withoutTrashed()
 */
	class BillingOrderAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingOrderAttendeeCreditNote
 *
 * @property int $id
 * @property int $order_id
 * @property int $credit_note_id
 * @property int $order_number
 * @property int $attendee_id
 * @property int $event_qty
 * @property float $event_discount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderAttendeeCreditNote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote whereCreditNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote whereEventDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote whereEventQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderAttendeeCreditNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|BillingOrderAttendeeCreditNote withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderAttendeeCreditNote withoutTrashed()
 */
	class BillingOrderAttendeeCreditNote extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingOrderCreditNote
 *
 * @property int $id
 * @property int $order_id
 * @property int $is_added_reporting
 * @property int $event_id
 * @property int $parent_id
 * @property int $attendee_id
 * @property int $sale_agent_id
 * @property int $sale_type
 * @property string $session_id
 * @property int $language_id
 * @property float $event_price
 * @property int $is_free
 * @property int $e_invoice
 * @property int $event_qty
 * @property float $event_discount
 * @property string $security
 * @property float $vat
 * @property float $vat_amount
 * @property int|null $payment_fee
 * @property float|null $payment_fee_vat
 * @property string $transaction_id
 * @property string|null $invoice_reference_no
 * @property float $grand_total
 * @property float $reporting_panel_total
 * @property float $corrected_total
 * @property float $summary_sub_total
 * @property int $total_attendee
 * @property string $discount_type
 * @property string $code
 * @property int $coupon_id
 * @property float $discount_amount
 * @property float|null $quantity_discount
 * @property string $order_date
 * @property int $eventsite_currency
 * @property string $order_number
 * @property int $billing_quantity
 * @property string $status
 * @property int $is_cancelled_wcn
 * @property string|null $comments
 * @property int $is_voucher
 * @property int $is_payment_received
 * @property string|null $payment_received_date
 * @property string $order_type
 * @property int $is_waitinglist
 * @property int $is_tango
 * @property string $dibs_dump
 * @property string|null $user_agent
 * @property string|null $session_data
 * @property int $is_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $hide_first_billing_item_description
 * @property string $credit_note_create_date
 * @property int $to_be_fetched
 * @property int|null $new_imp_flag
 * @property int $is_updated_qty
 * @property int $item_level_vat
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderCreditNote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereBillingQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereCorrectedTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereCreditNoteCreateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereDibsDump($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereEInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereEventDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereEventPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereEventQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereEventsiteCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereGrandTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereHideFirstBillingItemDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereInvoiceReferenceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereIsAddedReporting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereIsCancelledWcn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereIsFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereIsPaymentReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereIsTango($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereIsUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereIsUpdatedQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereIsVoucher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereIsWaitinglist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereItemLevelVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereNewImpFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereOrderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote wherePaymentFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote wherePaymentFeeVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote wherePaymentReceivedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereQuantityDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereReportingPanelTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereSaleAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereSaleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereSecurity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereSessionData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereSummarySubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereToBeFetched($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereTotalAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderCreditNote whereVatAmount($value)
 * @method static \Illuminate\Database\Query\Builder|BillingOrderCreditNote withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderCreditNote withoutTrashed()
 */
	class BillingOrderCreditNote extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingOrderLog
 *
 * @property int $id
 * @property int $event_id
 * @property int $organizer_id
 * @property int $order_id
 * @property string $field_name
 * @property string $update_date
 * @property string $update_date_time
 * @property string $data_log
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog whereDataLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog whereFieldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog whereUpdateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog whereUpdateDateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|BillingOrderLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderLog withoutTrashed()
 */
	class BillingOrderLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingOrderRuleLog
 *
 * @property int $id
 * @property int $rule_id
 * @property int $order_id
 * @property int $item_id
 * @property int $item_qty
 * @property int $rule_qty
 * @property string|null $discount_type
 * @property float $item_price
 * @property float $rule_discount
 * @property float $item_discount
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderRuleLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereItemDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereItemPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereItemQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereRuleDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereRuleQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingOrderRuleLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|BillingOrderRuleLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingOrderRuleLog withoutTrashed()
 */
	class BillingOrderRuleLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingVoucher
 *
 * @property int $id
 * @property string $type
 * @property int $discount_type
 * @property float $price
 * @property string $expiry_date
 * @property string $usage
 * @property int $event_id
 * @property int $status
 * @property string $code
 * @property int|null $qty_status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillingVoucherInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillingVoucherItem[] $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillingOrder[] $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingVoucher onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereQtyStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucher whereUsage($value)
 * @method static \Illuminate\Database\Query\Builder|BillingVoucher withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingVoucher withoutTrashed()
 */
	class BillingVoucher extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingVoucherInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $voucher_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingVoucherInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherInfo whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherInfo whereVoucherId($value)
 * @method static \Illuminate\Database\Query\Builder|BillingVoucherInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingVoucherInfo withoutTrashed()
 */
	class BillingVoucherInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BillingVoucherItem
 *
 * @property int $id
 * @property int $voucher_id
 * @property int|null $discount_type
 * @property float|null $price
 * @property string $useage
 * @property int $item_id
 * @property string $item_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|BillingVoucherItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem whereUseage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillingVoucherItem whereVoucherId($value)
 * @method static \Illuminate\Database\Query\Builder|BillingVoucherItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingVoucherItem withoutTrashed()
 */
	class BillingVoucherItem extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CheckInLog
 *
 * @property int $id
 * @property string $checkin
 * @property string $checkout
 * @property int $event_id
 * @property int $organizer_id
 * @property int $attendee_id
 * @property int $admin_id
 * @property string|null $type_name
 * @property int|null $type_id
 * @property string $data
 * @property int $status
 * @property string|null $delegate
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\CheckInUser $adminUser
 * @property-read \App\Models\Attendee $attendee
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendee[] $attendees
 * @property-read int|null $attendees_count
 * @property-read \Illuminate\Database\Eloquent\Collection|CheckInLog[] $delegates
 * @property-read int|null $delegates_count
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|CheckInLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereCheckin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereCheckout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereDelegate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereTypeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|CheckInLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CheckInLog withoutTrashed()
 */
	class CheckInLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CheckInUser
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckInUser whereUpdatedAt($value)
 */
	class CheckInUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Competition
 *
 * @property int $id
 * @property int $organizer_id
 * @property int $event_id
 * @property int $attendee_id
 * @property string $from_company_name
 * @property string $from_name
 * @property string $title
 * @property string $from_email
 * @property string $from_phone
 * @property string $to_company_name
 * @property string $to_name
 * @property string $to_email
 * @property string $to_phone
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Competition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Competition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Competition query()
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereFromCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereFromEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereFromName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereFromPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereToCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereToEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereToName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereToPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competition whereUpdatedAt($value)
 */
	class Competition extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CompetitionSetting
 *
 * @property int $id
 * @property int $event_id
 * @property string $template
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CompetitionSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetitionSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetitionSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetitionSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetitionSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetitionSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetitionSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetitionSetting whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetitionSetting whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetitionSetting whereUpdatedAt($value)
 */
	class CompetitionSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Country
 *
 * @property int $id
 * @property string|null $code_2
 * @property string $name
 * @property int $languages_id
 * @property string $language
 * @property string $language_name
 * @property int $parent_id
 * @property string $full_name
 * @property string|null $code_1
 * @property string|null $numcode
 * @property string|null $un_member
 * @property int|null $calling_code
 * @property string|null $cctld
 * @property string $alias
 * @property string $lat
 * @property string $lon
 * @property string $gmt
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCallingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCctld($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCode1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCode2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereGmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLanguageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereNumcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereUnMember($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereUpdatedAt($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CronLog
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CronLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CronLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|CronLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CronLog query()
 * @method static \Illuminate\Database\Query\Builder|CronLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CronLog withoutTrashed()
 */
	class CronLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CronPushNotification
 *
 * @property int $id
 * @property int $organizer_id
 * @property int $event_id
 * @property string $deviceType
 * @property string $deviceToken
 * @property int $alert_id
 * @property string $alert_date
 * @property string $alert_time
 * @property string $alertTtile
 * @property string $alertDescription
 * @property int $badge_count
 * @property string $status
 * @property string $responce
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\EventAlert $alert
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification newQuery()
 * @method static \Illuminate\Database\Query\Builder|CronPushNotification onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereAlertDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereAlertDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereAlertTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereAlertTtile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereBadgeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereDeviceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereResponce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CronPushNotification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|CronPushNotification withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CronPushNotification withoutTrashed()
 */
	class CronPushNotification extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CustomizeSetting
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property string $value
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CustomizeSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomizeSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomizeSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomizeSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomizeSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomizeSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomizeSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomizeSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomizeSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomizeSetting whereValue($value)
 */
	class CustomizeSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DateFormat
 *
 * @property int $id
 * @property string $name
 * @property string $format
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat query()
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat whereUpdatedAt($value)
 */
	class DateFormat extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Directory
 *
 * @property int $id
 * @property int $parent_id
 * @property int $other
 * @property int $agenda_id
 * @property int $event_id
 * @property int $speaker_id
 * @property int $sponsor_id
 * @property int $exhibitor_id
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Directory[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DirectoryFile[] $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DirectoryInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|Directory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Directory newQuery()
 * @method static \Illuminate\Database\Query\Builder|Directory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Directory query()
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereExhibitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereSpeakerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereSponsorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Directory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Directory withoutTrashed()
 */
	class Directory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DirectoryFile
 *
 * @property int $id
 * @property int $organizer_id
 * @property int $directory_id
 * @property int $parent_id
 * @property int $file_size
 * @property string $path
 * @property string $start_date
 * @property string $start_time
 * @property int $sort_order
 * @property int|null $s3
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Directory $directories
 * @property-read \App\Models\DirectoryFileNote|null $file_notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FileInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile newQuery()
 * @method static \Illuminate\Database\Query\Builder|DirectoryFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereDirectoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereS3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|DirectoryFile withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DirectoryFile withoutTrashed()
 */
	class DirectoryFile extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DirectoryFileNote
 *
 * @property int $id
 * @property int $event_id
 * @property string $notes
 * @property int $attendee_id
 * @property int $file_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFileNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFileNote newQuery()
 * @method static \Illuminate\Database\Query\Builder|DirectoryFileNote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFileNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFileNote whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFileNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFileNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFileNote whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFileNote whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFileNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFileNote whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryFileNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|DirectoryFileNote withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DirectoryFileNote withoutTrashed()
 */
	class DirectoryFileNote extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DirectoryInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $directory_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|DirectoryInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo whereDirectoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|DirectoryInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DirectoryInfo withoutTrashed()
 */
	class DirectoryInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DirectorySetting
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $event_id
 * @property string $alies
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|DirectorySetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting whereAlies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectorySetting whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|DirectorySetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DirectorySetting withoutTrashed()
 */
	class DirectorySetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DynamicsToken
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $org_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $access_token
 * @property string|null $refresh_token
 * @property string|null $id_token
 * @property string|null $expires_at
 * @property int $authorized
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken whereAuthorized($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken whereIdToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken whereOrgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicsToken whereUpdatedAt($value)
 */
	class DynamicsToken extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EconomicCustomer
 *
 * @property int $customerNumber
 * @property string|null $email
 * @property string|null $currency
 * @property int|null $paymentTermsNumber
 * @property int|null $customerGroupNumber
 * @property string|null $address
 * @property string|null $balance
 * @property string|null $dueAmount
 * @property string|null $corporateIdentificationNumber
 * @property string|null $city
 * @property string|null $country
 * @property string|null $ean
 * @property string|null $name
 * @property string|null $zip
 * @property string|null $website
 * @property int|null $vatZoneNumber
 * @property int|null $layoutNumber
 * @property int|null $customerContactNumber
 * @property string|null $lastUpdated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer newQuery()
 * @method static \Illuminate\Database\Query\Builder|EconomicCustomer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer query()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereCorporateIdentificationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereCustomerContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereCustomerGroupNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereCustomerNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereDueAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereEan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereLayoutNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer wherePaymentTermsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereVatZoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicCustomer whereZip($value)
 * @method static \Illuminate\Database\Query\Builder|EconomicCustomer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EconomicCustomer withoutTrashed()
 */
	class EconomicCustomer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EconomicDepartment
 *
 * @property int $departmentNumber
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicDepartment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicDepartment newQuery()
 * @method static \Illuminate\Database\Query\Builder|EconomicDepartment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicDepartment query()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicDepartment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicDepartment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicDepartment whereDepartmentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicDepartment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicDepartment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EconomicDepartment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EconomicDepartment withoutTrashed()
 */
	class EconomicDepartment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EconomicInvoice
 *
 * @property int $bookedInvoiceNumber
 * @property string|null $date
 * @property string|null $currency
 * @property string|null $exchangeRate
 * @property string|null $netAmount
 * @property string|null $netAmountInBaseCurrency
 * @property string|null $grossAmount
 * @property string|null $grossAmountInBaseCurrency
 * @property string|null $vatAmount
 * @property string|null $roundingAmount
 * @property string|null $remainder
 * @property int|null $remainderInBaseCurrency
 * @property string|null $dueDate
 * @property int|null $paymentTermsNumber
 * @property int|null $daysOfCredit
 * @property string|null $paymentTermsName
 * @property string|null $paymentTermsType
 * @property int|null $customerNumber
 * @property string|null $recipient_name
 * @property string|null $recipient_address
 * @property string|null $recipient_zip
 * @property string|null $recipient_city
 * @property string|null $recipient_country
 * @property string|null $recipient_ean
 * @property int|null $customerContactNumber
 * @property int|null $vatZoneNumber
 * @property int|null $layoutNumber
 * @property string|null $unitNetPrice
 * @property string|null $delivery_address
 * @property string|null $deliveryTerms License from date
 * @property string|null $deliveryDate License to date
 * @property string|null $type 1 = (4 + 3), 2 = 5, 3 = Other Groups, 4 = No Groups
 * @property int|null $is_credit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice newQuery()
 * @method static \Illuminate\Database\Query\Builder|EconomicInvoice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereBookedInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereCustomerContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereCustomerNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereDaysOfCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereDeliveryAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereDeliveryTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereExchangeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereGrossAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereGrossAmountInBaseCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereIsCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereLayoutNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereNetAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereNetAmountInBaseCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice wherePaymentTermsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice wherePaymentTermsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice wherePaymentTermsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereRecipientAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereRecipientCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereRecipientCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereRecipientEan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereRecipientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereRecipientZip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereRemainder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereRemainderInBaseCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereRoundingAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereUnitNetPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereVatAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoice whereVatZoneNumber($value)
 * @method static \Illuminate\Database\Query\Builder|EconomicInvoice withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EconomicInvoice withoutTrashed()
 */
	class EconomicInvoice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EconomicInvoiceProduct
 *
 * @property int $id
 * @property int|null $lineNumber
 * @property int|null $sortKey
 * @property string|null $description
 * @property string|null $quantity
 * @property string|null $unitNetPrice
 * @property string|null $discountPercentage
 * @property string|null $unitCostPrice
 * @property string|null $vatRate
 * @property string|null $totalNetAmount
 * @property int|null $productNumber
 * @property int|null $productGroupNumber
 * @property int|null $bookedInvoiceNumber
 * @property int|null $unitNumber
 * @property int|null $departmentalDistributionNumber
 * @property int|null $is_credit
 * @property int|null $customerNumber
 * @property string|null $deliveryTerms License from date
 * @property string|null $deliveryDate License to date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct newQuery()
 * @method static \Illuminate\Database\Query\Builder|EconomicInvoiceProduct onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereBookedInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereCustomerNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereDeliveryTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereDepartmentalDistributionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereDiscountPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereIsCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereLineNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereProductGroupNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereProductNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereSortKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereTotalNetAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereUnitCostPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereUnitNetPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereUnitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicInvoiceProduct whereVatRate($value)
 * @method static \Illuminate\Database\Query\Builder|EconomicInvoiceProduct withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EconomicInvoiceProduct withoutTrashed()
 */
	class EconomicInvoiceProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EconomicProduct
 *
 * @property int $id
 * @property string $productNumber
 * @property string|null $name
 * @property string|null $description
 * @property string|null $recommendedPrice
 * @property string|null $salesPrice
 * @property string|null $lastUpdated
 * @property int|null $productGroupNumber
 * @property int|null $barred
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct newQuery()
 * @method static \Illuminate\Database\Query\Builder|EconomicProduct onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereBarred($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereProductGroupNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereProductNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereRecommendedPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereSalesPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EconomicProduct withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EconomicProduct withoutTrashed()
 */
	class EconomicProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EconomicProductGroup
 *
 * @property int $productGroupNumber
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProductGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProductGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|EconomicProductGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProductGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProductGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProductGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProductGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProductGroup whereProductGroupNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EconomicProductGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EconomicProductGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EconomicProductGroup withoutTrashed()
 */
	class EconomicProductGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EmailMarketingFolder
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingFolder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingFolder newQuery()
 * @method static \Illuminate\Database\Query\Builder|EmailMarketingFolder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingFolder query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingFolder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingFolder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingFolder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingFolder whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingFolder whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingFolder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EmailMarketingFolder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EmailMarketingFolder withoutTrashed()
 */
	class EmailMarketingFolder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EmailMarketingTemplate
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $name
 * @property string $list_type
 * @property int $folder_id
 * @property string|null $image
 * @property string|null $template
 * @property \Illuminate\Support\Carbon $created_at
 * @property int $created_by
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|EmailMarketingTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereFolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereListType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailMarketingTemplate whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|EmailMarketingTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EmailMarketingTemplate withoutTrashed()
 */
	class EmailMarketingTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EmailTemplateInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $template_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EmailTemplateInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplateInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EmailTemplateInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EmailTemplateInfo withoutTrashed()
 */
	class EmailTemplateInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Event
 *
 * @property int $id
 * @property string $organizer_name
 * @property string $name
 * @property string $url
 * @property string $tickets_left
 * @property string $start_date
 * @property string $end_date
 * @property string $start_time
 * @property string $end_time
 * @property string $cancellation_date
 * @property string $registration_end_date
 * @property int $organizer_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $language_id
 * @property int $timezone_id
 * @property int $country_id
 * @property int $office_country_id
 * @property string $latitude
 * @property string $longitude
 * @property int $owner_id
 * @property string $export_setting
 * @property int $show_native_app_link
 * @property int $organizer_site
 * @property string|null $native_app_acessed_date
 * @property string $native_app_timer
 * @property string|null $white_label_sender_name
 * @property string|null $white_label_sender_email
 * @property int|null $is_template
 * @property int|null $is_advance_template
 * @property int|null $is_wizard_template
 * @property int $type 0 = event center / 1 = plugnplay
 * @property int|null $is_registration
 * @property int|null $is_app
 * @property int|null $is_map
 * @property int|null $template_id
 * @property-read \App\Models\AttendeeSetting|null $attendee_settings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendee[] $attendees
 * @property-read int|null $attendees_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Language[] $languages
 * @property-read int|null $languages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendee[] $orderAttendees
 * @property-read int|null $order_attendees_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSetting[] $settings
 * @property-read int|null $settings_count
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Query\Builder|Event onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCancellationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereExportSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsAdvanceTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsMap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsRegistration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsWizardTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNativeAppAcessedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNativeAppTimer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereOfficeCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereOrganizerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereOrganizerSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereRegistrationEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereShowNativeAppLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereTicketsLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereWhiteLabelSenderEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereWhiteLabelSenderName($value)
 * @method static \Illuminate\Database\Query\Builder|Event withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Event withoutTrashed()
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgenda
 *
 * @property int $id
 * @property int $event_id
 * @property string $start_date
 * @property string $start_time
 * @property string $link_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $workshop_id
 * @property int $qa
 * @property int $ticket
 * @property int $enable_checkin
 * @property int $enable_speakerlist
 * @property int $hide_on_registrationsite
 * @property int|null $hide_on_app
 * @property int|null $only_for_qa
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventGroup[] $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgendaInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendee[] $program_speakers
 * @property-read int|null $program_speakers_count
 * @property-read \App\Models\EventWorkshop $program_workshop
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventTrack[] $tracks
 * @property-read int|null $tracks_count
 * @property-read \App\Models\AgendaVideo|null $video
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgendaVideo[] $videos
 * @property-read int|null $videos_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAgenda onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereEnableCheckin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereEnableSpeakerlist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereHideOnApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereHideOnRegistrationsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereOnlyForQa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereQa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereTicket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgenda whereWorkshopId($value)
 * @method static \Illuminate\Database\Query\Builder|EventAgenda withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAgenda withoutTrashed()
 */
	class EventAgenda extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgendaAttendee
 *
 * @property int $id
 * @property int $attendee_id
 * @property int $agenda_id
 * @property int $fav
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendee whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendee whereFav($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendee whereUpdatedAt($value)
 */
	class EventAgendaAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgendaAttendeeAttached
 *
 * @property int $id
 * @property int $attendee_id
 * @property int $agenda_id
 * @property int $added_by
 * @property string $linked_from
 * @property int $link_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaAttendeeAttached onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached whereLinkedFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaAttendeeAttached whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAgendaAttendeeAttached withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaAttendeeAttached withoutTrashed()
 */
	class EventAgendaAttendeeAttached extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgendaCheckInSession
 *
 * @property int $id
 * @property int $event_id
 * @property int $agenda_id
 * @property int $is_active
 * @property string $session_date
 * @property string $start_time
 * @property string $end_time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaCheckInSession onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession whereSessionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckInSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAgendaCheckInSession withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaCheckInSession withoutTrashed()
 */
	class EventAgendaCheckInSession extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgendaCheckinHistory
 *
 * @property int $id
 * @property int $event_id
 * @property int $agenda_id
 * @property int $session_id
 * @property int $attendee_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckinHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckinHistory newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaCheckinHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckinHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckinHistory whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckinHistory whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckinHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckinHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckinHistory whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckinHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckinHistory whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaCheckinHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAgendaCheckinHistory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaCheckinHistory withoutTrashed()
 */
	class EventAgendaCheckinHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgendaGroup
 *
 * @property int $id
 * @property int $agenda_id
 * @property int $group_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaGroup whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAgendaGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaGroup withoutTrashed()
 */
	class EventAgendaGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgendaRating
 *
 * @property int $id
 * @property int $rate
 * @property string $comment
 * @property int $agenda_id
 * @property int $attendee_id
 * @property int $is_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaRating onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating whereIsUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaRating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAgendaRating withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaRating withoutTrashed()
 */
	class EventAgendaRating extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgendaSpeaker
 *
 * @property int $id
 * @property int $event_id
 * @property int $eventsite_show_home
 * @property int $agenda_id
 * @property int $attendee_id
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Attendee $attendee
 * @property-read \App\Models\Agenda $program
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaSpeaker onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker whereEventsiteShowHome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeaker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAgendaSpeaker withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaSpeaker withoutTrashed()
 */
	class EventAgendaSpeaker extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgendaSpeakerlistHistory
 *
 * @property int $id
 * @property int $event_id
 * @property int $agenda_id
 * @property int $session_id
 * @property int $attendee_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistHistory newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaSpeakerlistHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistHistory whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistHistory whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistHistory whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistHistory whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAgendaSpeakerlistHistory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaSpeakerlistHistory withoutTrashed()
 */
	class EventAgendaSpeakerlistHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgendaSpeakerlistSession
 *
 * @property int $id
 * @property int $event_id
 * @property int $agenda_id
 * @property int $is_active
 * @property string $session_date
 * @property string $start_time
 * @property string $end_time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaSpeakerlistSession onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession whereSessionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaSpeakerlistSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAgendaSpeakerlistSession withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaSpeakerlistSession withoutTrashed()
 */
	class EventAgendaSpeakerlistSession extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgendaTrack
 *
 * @property int $id
 * @property int $track_id
 * @property int $agenda_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTrack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTrack newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaTrack onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTrack query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTrack whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTrack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTrack whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTrack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTrack whereTrackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTrack whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAgendaTrack withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaTrack withoutTrashed()
 */
	class EventAgendaTrack extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAgendaTurnList
 *
 * @property int $id
 * @property string $status
 * @property int|null $sort_order
 * @property int $agenda_id
 * @property int $attendee_id
 * @property string $speech_start_time
 * @property string|null $moderator_speech_start_time
 * @property string|null $moderator_speech_end_time
 * @property string $notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Attendee $attendee
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaTurnList onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereModeratorSpeechEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereModeratorSpeechStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereSpeechStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAgendaTurnList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAgendaTurnList withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAgendaTurnList withoutTrashed()
 */
	class EventAgendaTurnList extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAlert
 *
 * @property int $id
 * @property int $event_id
 * @property int $pre_schedule 0=no; 1=yes
 * @property string $alert_date
 * @property string $alert_time
 * @property string $sendto
 * @property int $alert_email 0=no; 1=yes
 * @property int $alert_sms 0=no; 1=yes
 * @property int $status 1=PENDING,  2=SENT,
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventAlertAgenda[] $agendas
 * @property-read int|null $agendas_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventAlertAttendee[] $attendees
 * @property-read int|null $attendees_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventExhibitorAlert[] $exhibitors
 * @property-read int|null $exhibitors_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventAlertGroup[] $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventAlertIndividual[] $individuals
 * @property-read int|null $individuals_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventAlertInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventPollAlert[] $polls
 * @property-read int|null $polls_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSponsorAlert[] $sponsors
 * @property-read int|null $sponsors_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSurveyAlert[] $surveys
 * @property-read int|null $surveys_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventWorkshopAlert[] $workshops
 * @property-read int|null $workshops_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAlert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert whereAlertDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert whereAlertEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert whereAlertSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert whereAlertTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert wherePreSchedule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert whereSendto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAlert withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAlert withoutTrashed()
 */
	class EventAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAlertAgenda
 *
 * @property int $id
 * @property int $agenda_id
 * @property int $alert_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAgenda newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAgenda newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAlertAgenda onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAgenda query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAgenda whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAgenda whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAgenda whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAgenda whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAgenda whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAgenda whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAgenda whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAlertAgenda withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAlertAgenda withoutTrashed()
 */
	class EventAlertAgenda extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAlertAttendee
 *
 * @property int $id
 * @property string $date
 * @property int $attendee_id
 * @property int $alert_id
 * @property int $status 1==Not Read, 2 ==Read
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAttendee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAttendee whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAttendee whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAttendee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertAttendee whereUpdatedAt($value)
 */
	class EventAlertAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAlertGroup
 *
 * @property int $id
 * @property int $alert_id
 * @property int $group_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAlertGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertGroup whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertGroup whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAlertGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAlertGroup withoutTrashed()
 */
	class EventAlertGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAlertIndividual
 *
 * @property int $id
 * @property string $date
 * @property string $value
 * @property int $attendee_id
 * @property int $alert_id
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAlertIndividual onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertIndividual whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventAlertIndividual withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAlertIndividual withoutTrashed()
 */
	class EventAlertIndividual extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAlertInfo
 *
 * @property int $id
 * @property int $alert_id
 * @property string $name
 * @property string $value
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAlertInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAlertInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventAlertInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAlertInfo withoutTrashed()
 */
	class EventAlertInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendee
 *
 * @property int $id
 * @property int $email_sent
 * @property int $sms_sent
 * @property int $login_yet
 * @property int $status
 * @property int $attendee_id
 * @property int $event_id
 * @property string $speaker 1==Yes, 0==No
 * @property string $sponser 1==Yes, 0==No
 * @property string $exhibitor 1==Yes, 0==No
 * @property int $attendee_type
 * @property int $default_language_id
 * @property string $device_token
 * @property string $device_type
 * @property int $app_invite_sent
 * @property int $is_active
 * @property string $verification_id
 * @property int $gdpr
 * @property int $allow_vote
 * @property int $allow_gallery
 * @property int $ask_to_apeak
 * @property int $type_resource
 * @property int $accept_foods_allergies
 * @property string $native_app_forgot_password_code
 * @property string $native_app_forgot_password_code_created_at
 * @property int|null $custom_field_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $allow_my_document
 * @property-read \App\Models\Attendee $attendee
 * @property-read \App\Models\Attendee $attendees
 * @property-read \App\Models\Event $event
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereAcceptFoodsAllergies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereAllowGallery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereAllowMyDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereAllowVote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereAppInviteSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereAskToApeak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereAttendeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereCustomFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereDefaultLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereDeviceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereEmailSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereExhibitor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereGdpr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereLoginYet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereNativeAppForgotPasswordCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereNativeAppForgotPasswordCodeCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereSmsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereSpeaker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereSponser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereTypeResource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendee whereVerificationId($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendee withoutTrashed()
 */
	class EventAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeAppInviteLog
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $email_sent
 * @property int $sms_sent
 * @property string $email_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeAppInviteLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog whereEmailDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog whereEmailSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog whereSmsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeAppInviteLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeAppInviteLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeAppInviteLog withoutTrashed()
 */
	class EventAttendeeAppInviteLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeEmailHistory
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property string $email_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistory newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeEmailHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistory whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistory whereEmailDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistory whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeEmailHistory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeEmailHistory withoutTrashed()
 */
	class EventAttendeeEmailHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeEmailHistoryInvite
 *
 * @property int $id
 * @property int $event_id
 * @property string $email
 * @property string $email_date
 * @property int $invitation_accepted
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistoryInvite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistoryInvite newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeEmailHistoryInvite onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistoryInvite query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistoryInvite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistoryInvite whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistoryInvite whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistoryInvite whereEmailDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistoryInvite whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistoryInvite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistoryInvite whereInvitationAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeEmailHistoryInvite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeEmailHistoryInvite withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeEmailHistoryInvite withoutTrashed()
 */
	class EventAttendeeEmailHistoryInvite extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeGroup
 *
 * @property int $id
 * @property int $attendee_id
 * @property int $group_id
 * @property string $linked_from
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\EventGroup $group
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeGroup whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeGroup whereLinkedFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeGroup withoutTrashed()
 */
	class EventAttendeeGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeImage
 *
 * @property int $id
 * @property int $attendee_id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeImage whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeImage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeImage whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeImage whereUpdatedAt($value)
 */
	class EventAttendeeImage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeePrivateDocument
 *
 * @property int $id
 * @property string $file_caption
 * @property string $uploaded_filename
 * @property string $stored_filename
 * @property int $filesize
 * @property int $event_id
 * @property int $attendee_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeePrivateDocument onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument whereFileCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument whereFilesize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument whereStoredFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocument whereUploadedFilename($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeePrivateDocument withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeePrivateDocument withoutTrashed()
 */
	class EventAttendeePrivateDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeePrivateDocumentShare
 *
 * @property int $id
 * @property int $event_id
 * @property int $shared_by
 * @property int $attendee_id
 * @property int $private_document_id
 * @property int|null $entity_id
 * @property string|null $entity_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $enabled 1=Yes,0=No
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeePrivateDocumentShare onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare wherePrivateDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare whereSharedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeePrivateDocumentShare whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeePrivateDocumentShare withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeePrivateDocumentShare withoutTrashed()
 */
	class EventAttendeePrivateDocumentShare extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeSurvey
 *
 * @property int $id
 * @property int $sort_order
 * @property string $user_type
 * @property int $user_id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $hub_admin_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurvey onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey whereHubAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurvey whereUserType($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurvey withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurvey withoutTrashed()
 */
	class EventAttendeeSurvey extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeSurveyAnswer
 *
 * @property int $id
 * @property int $sort_order
 * @property int $question_id
 * @property int $correct
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswer newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyAnswer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswer whereCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswer whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyAnswer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyAnswer withoutTrashed()
 */
	class EventAttendeeSurveyAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeSurveyAnswerInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $answer_id
 * @property int $question_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyAnswerInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyAnswerInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyAnswerInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyAnswerInfo withoutTrashed()
 */
	class EventAttendeeSurveyAnswerInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeSurveyInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $languages_id
 * @property int $survey_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyInfo whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyInfo withoutTrashed()
 */
	class EventAttendeeSurveyInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeSurveyQuestion
 *
 * @property int $id
 * @property string $question_type
 * @property string $result_chart_type
 * @property string $anonymous
 * @property string $required_question
 * @property string $enable_comments
 * @property int $sort_order
 * @property string $start_date
 * @property string $end_date
 * @property int $survey_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereEnableComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereQuestionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereRequiredQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereResultChartType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyQuestion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyQuestion withoutTrashed()
 */
	class EventAttendeeSurveyQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeSurveyQuestionInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $question_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestionInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestionInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyQuestionInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestionInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestionInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestionInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestionInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestionInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestionInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestionInfo whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestionInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyQuestionInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyQuestionInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyQuestionInfo withoutTrashed()
 */
	class EventAttendeeSurveyQuestionInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeSurveyResult
 *
 * @property int $id
 * @property string $answer
 * @property string $comments
 * @property int $event_id
 * @property int $survey_id
 * @property int $attendee_id
 * @property int $question_id
 * @property int $answer_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyResult onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyResult withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyResult withoutTrashed()
 */
	class EventAttendeeSurveyResult extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeSurveyResultScore
 *
 * @property int $id
 * @property int $score
 * @property int $survey_id
 * @property int $question_id
 * @property int $attendee_id
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyResultScore onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeSurveyResultScore whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyResultScore withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventAttendeeSurveyResultScore withoutTrashed()
 */
	class EventAttendeeSurveyResultScore extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventAttendeeType
 *
 * @property int $id
 * @property int $event_id
 * @property int $languages_id
 * @property int $sort_order
 * @property string $alias
 * @property string $attendee_type
 * @property int $is_basic
 * @property int $status 0=inactive, 1=active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType whereAttendeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType whereIsBasic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventAttendeeType whereUpdatedAt($value)
 */
	class EventAttendeeType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventBadge
 *
 * @property int $id
 * @property int $event_id
 * @property int $template_type
 * @property string $heading_color
 * @property string $company_color
 * @property string $tracks_color
 * @property string $delegate_Color
 * @property string $table_Color
 * @property string $logo
 * @property string $logoType
 * @property string $footer_bg_color
 * @property string $footer_text_color
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventBadge onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereCompanyColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereDelegateColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereFooterBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereFooterTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereHeadingColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereLogoType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereTableColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereTemplateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereTracksColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventBadge withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventBadge withoutTrashed()
 */
	class EventBadge extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventBadgeCustom
 *
 * @property int $id
 * @property int $event_id
 * @property int $organizer_id
 * @property string $name
 * @property int $size
 * @property string $body
 * @property string $logo
 * @property string $background
 * @property string|null $badgefor
 * @property int|null $badgeTypeId
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventBadgeCustom onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereBadgeTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereBadgefor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeCustom whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventBadgeCustom withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventBadgeCustom withoutTrashed()
 */
	class EventBadgeCustom extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventBadgeDesign
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property string $type
 * @property int $attendee_type
 * @property string $width
 * @property string $height
 * @property int $mirror
 * @property int $is_default
 * @property string $body
 * @property int $IsName
 * @property string $nameLocation
 * @property int $IsFirstName
 * @property string $firstnameLocation
 * @property int $IsLastName
 * @property string $lastnameLocation
 * @property int $IsCompanyName
 * @property string $companyNameLocation
 * @property int $IsTitle
 * @property string $titleLocation
 * @property int $IsCompanyAddress
 * @property string $companyAddressLocation
 * @property int $IsPrivateAddress
 * @property string $privateAddressLocation
 * @property int $IsTelephone
 * @property string $telephoneLocation
 * @property int $IsMobile
 * @property string $mobileLocation
 * @property int $IsMobile_2 Use for drop down 2
 * @property string $mobile2Location Use for drop down 2 location
 * @property int $IsMobile_3 Use for drop down 3
 * @property string $mobile3Location Use for drop down 3 location
 * @property int $IsMobile_4 Use for drop down 4
 * @property string $mobile4Location Use for drop down 4 location
 * @property int $IsMobile_5 Use for drop down 5
 * @property string $mobile5Location Use for drop down 5 location
 * @property int $IsMobile_6 Use for drop down 6
 * @property string $mobile6Location Use for drop down 6 location
 * @property int $IsMobile_7 Use for drop down 7
 * @property string $mobile7Location Use for drop down 7 location
 * @property int $IsMobile_8 Use for drop down 8
 * @property string $mobile8Location Use for drop down 8 location
 * @property int $IsMobile_9 Use for drop down 9
 * @property string $mobile9Location Use for drop down 9 location
 * @property int $IsMobile_10 Use for drop down 10
 * @property string $mobile10Location Use for drop down 10 location
 * @property int $Interests
 * @property string $interestsLocation
 * @property int $IsLogo
 * @property string $logoLocation
 * @property int $IsImage
 * @property string $imageLocation
 * @property string $imageName
 * @property int $IsBgImage
 * @property string $bgImageLocation
 * @property string $bgImageName
 * @property int $Textfield
 * @property string $textfieldLocation
 * @property int $IsEmail
 * @property string $emailLocation
 * @property int $IsProductArea
 * @property string $productAreaLocation
 * @property int $IsDepartment
 * @property string $departmentLocation
 * @property int $IsBarcode
 * @property string $barcodeLocation
 * @property int $IsCountry
 * @property string $countryLocation
 * @property int $IsOrganization
 * @property string $organizationLocation
 * @property int $IsDelegateNumber
 * @property string $delegateNumberLocation
 * @property int $IsNetworkGroup
 * @property string $networkGroupLocation
 * @property int $IsName_1
 * @property string $nameLocation_1
 * @property int $IsFirstName_1
 * @property string $firstnameLocation_1
 * @property int $IsLastName_1
 * @property string $lastnameLocation_1
 * @property int $IsCompanyName_1
 * @property string $companyNameLocation_1
 * @property int $IsTitle_1
 * @property string $titleLocation_1
 * @property int $IsBarcode_1
 * @property string $barcodeLocation_1
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventBadgeDesign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereAttendeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereBarcodeLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereBarcodeLocation1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereBgImageLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereBgImageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereCompanyAddressLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereCompanyNameLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereCompanyNameLocation1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereCountryLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereDelegateNumberLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereDepartmentLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereEmailLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereFirstnameLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereFirstnameLocation1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereImageLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereImageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereInterests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereInterestsLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsBarcode1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsBgImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsCompanyAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsCompanyName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsDelegateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsFirstName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsLastName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsMobile10($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsMobile2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsMobile3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsMobile4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsMobile5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsMobile6($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsMobile7($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsMobile8($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsMobile9($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsNetworkGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsPrivateAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsProductArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereIsTitle1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereLastnameLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereLastnameLocation1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereLogoLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereMirror($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereMobile10Location($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereMobile2Location($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereMobile3Location($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereMobile4Location($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereMobile5Location($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereMobile6Location($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereMobile7Location($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereMobile8Location($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereMobile9Location($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereMobileLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereNameLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereNameLocation1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereNetworkGroupLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereOrganizationLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign wherePrivateAddressLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereProductAreaLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereTelephoneLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereTextfield($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereTextfieldLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereTitleLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereTitleLocation1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeDesign whereWidth($value)
 * @method static \Illuminate\Database\Query\Builder|EventBadgeDesign withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventBadgeDesign withoutTrashed()
 */
	class EventBadgeDesign extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventBadgeSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $email_template_id
 * @property int $event_template_id
 * @property string $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeSetting whereEmailTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeSetting whereEventTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBadgeSetting whereUpdatedAt($value)
 */
	class EventBadgeSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventBanner
 *
 * @property int $id
 * @property int $event_id
 * @property int $sponsor_id
 * @property int $exhibitor_id
 * @property string $other_link_url
 * @property int $sort_order
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $start_date
 * @property string|null $end_date
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventBanner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereExhibitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereOtherLinkUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereSponsorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBanner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventBanner withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventBanner withoutTrashed()
 */
	class EventBanner extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventBannerInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $banner_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventBannerInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo whereBannerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannerInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventBannerInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventBannerInfo withoutTrashed()
 */
	class EventBannerInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventBannersSetting
 *
 * @property int $id
 * @property int $event_id
 * @property string $main_banner_position
 * @property string $native_banner_position
 * @property int $bannerads_orderby
 * @property int $display_banner
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting whereBanneradsOrderby($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting whereDisplayBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting whereMainBannerPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting whereNativeBannerPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventBannersSetting whereUpdatedAt($value)
 */
	class EventBannersSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCardType
 *
 * @property int $id
 * @property int $event_id
 * @property int $organizer_id
 * @property string $card_type
 * @property string $purchase_policy_inline_text
 * @property string $purchase_policy
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCardType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType whereCardType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType wherePurchasePolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType wherePurchasePolicyInlineText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCardType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventCardType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCardType withoutTrashed()
 */
	class EventCardType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCategory
 *
 * @property int $id
 * @property int $event_id
 * @property int $parent_id
 * @property string $color
 * @property int $sort_order
 * @property int $status
 * @property string $cat_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereCatType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCategory withoutTrashed()
 */
	class EventCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCategoryInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $category_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCategoryInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCategoryInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventCategoryInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCategoryInfo withoutTrashed()
 */
	class EventCategoryInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventChatMessageReadState
 *
 * @property int $message_id
 * @property int $user_id
 * @property string $read_date
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatMessageReadState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatMessageReadState newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventChatMessageReadState onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatMessageReadState query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatMessageReadState whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatMessageReadState whereReadDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatMessageReadState whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|EventChatMessageReadState withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventChatMessageReadState withoutTrashed()
 */
	class EventChatMessageReadState extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventChatThread
 *
 * @property int $id
 * @property int $event_id
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatThread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatThread newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventChatThread onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatThread query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatThread whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatThread whereId($value)
 * @method static \Illuminate\Database\Query\Builder|EventChatThread withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventChatThread withoutTrashed()
 */
	class EventChatThread extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventChatThreadParticipant
 *
 * @property int $thread_id
 * @property int $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatThreadParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatThreadParticipant newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventChatThreadParticipant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatThreadParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatThreadParticipant whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventChatThreadParticipant whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|EventChatThreadParticipant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventChatThreadParticipant withoutTrashed()
 */
	class EventChatThreadParticipant extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCheckInSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $status 0=disable, 1= enable
 * @property string|null $type
 * @property string|null $single_type
 * @property string|null $radius
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $address
 * @property int $gps_checkin
 * @property int $self_checkin
 * @property int|null $event_checkin
 * @property int|null $program_checkin
 * @property int|null $group_checkin
 * @property int|null $ticket_checkin
 * @property int|null $validate_program_checkin
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCheckInSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereEventCheckin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereGpsCheckin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereGroupCheckin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereProgramCheckin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereRadius($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereSelfCheckin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereSingleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereTicketCheckin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInSetting whereValidateProgramCheckin($value)
 * @method static \Illuminate\Database\Query\Builder|EventCheckInSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCheckInSetting withoutTrashed()
 */
	class EventCheckInSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCheckInTicketItem
 *
 * @property int $id
 * @property int $event_id
 * @property int $organizer_id
 * @property string|null $item_number
 * @property string $item_name
 * @property int|null $price
 * @property int|null $total_tickets
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $status 0=de-active
 * 1=active
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCheckInTicketItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem whereItemNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem whereTotalTickets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventCheckInTicketItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCheckInTicketItem withoutTrashed()
 */
	class EventCheckInTicketItem extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCheckInTicketItemInfo
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $value
 * @property int|null $ticket_item_id
 * @property int|null $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItemInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItemInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItemInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItemInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItemInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItemInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItemInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItemInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItemInfo whereTicketItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItemInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketItemInfo whereValue($value)
 */
	class EventCheckInTicketItemInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCheckInTicketOrder
 *
 * @property int $id
 * @property int $event_id
 * @property int $organizer_id
 * @property int $user_id
 * @property string $order_date
 * @property int $is_archive
 * @property string $status
 * @property string|null $user_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCheckInTicketOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder whereIsArchive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrder whereUserType($value)
 * @method static \Illuminate\Database\Query\Builder|EventCheckInTicketOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCheckInTicketOrder withoutTrashed()
 */
	class EventCheckInTicketOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCheckInTicketOrderAddon
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property int $addon_id
 * @property float|null $price
 * @property int $qty
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCheckInTicketOrderAddon onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon whereAddonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCheckInTicketOrderAddon whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|EventCheckInTicketOrderAddon withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCheckInTicketOrderAddon withoutTrashed()
 */
	class EventCheckInTicketOrderAddon extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCloneLog
 *
 * @property int $id
 * @property int $from_event
 * @property int $to_event
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Event $fromEvent
 * @property-read \App\Models\Event $toEvent
 * @method static \Illuminate\Database\Eloquent\Builder|EventCloneLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCloneLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCloneLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCloneLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCloneLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCloneLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCloneLog whereFromEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCloneLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCloneLog whereToEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCloneLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventCloneLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCloneLog withoutTrashed()
 */
	class EventCloneLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventComment
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $parent_id
 * @property int $image_id
 * @property string $comment
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventComment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventComment withoutTrashed()
 */
	class EventComment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCommentLike
 *
 * @property int $id
 * @property int $attendee_id
 * @property int $comment_id
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCommentLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCommentLike newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCommentLike onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCommentLike query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCommentLike whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCommentLike whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCommentLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCommentLike whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCommentLike whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCommentLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCommentLike whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCommentLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventCommentLike withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCommentLike withoutTrashed()
 */
	class EventCommentLike extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCompetitionTemplate
 *
 * @property int $id
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCompetitionTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCompetitionTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCompetitionTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCompetitionTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCompetitionTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCompetitionTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCompetitionTemplate whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCompetitionTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCompetitionTemplate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCompetitionTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventCompetitionTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCompetitionTemplate withoutTrashed()
 */
	class EventCompetitionTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCreditNoteOrderHotel
 *
 * @property int $id
 * @property int $hotel_id
 * @property int $order_id
 * @property string $name
 * @property int $price
 * @property string $price_type
 * @property float $vat
 * @property float $vat_price
 * @property int $rooms
 * @property string $checkin
 * @property string $checkout
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereCheckin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereCheckout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel wherePriceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCreditNoteOrderHotel whereVatPrice($value)
 */
	class EventCreditNoteOrderHotel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCustomField
 *
 * @property int $id
 * @property int $event_id
 * @property int $parent_id
 * @property int $sort_order
 * @property int $change_answer
 * @property int $registration_flow
 * @property int $profile_detail
 * @property int $is_require
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventCustomFieldInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCustomField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField whereChangeAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField whereIsRequire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField whereProfileDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField whereRegistrationFlow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventCustomField withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCustomField withoutTrashed()
 */
	class EventCustomField extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCustomFieldInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $custom_field_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomFieldInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomFieldInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCustomFieldInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomFieldInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomFieldInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomFieldInfo whereCustomFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomFieldInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomFieldInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomFieldInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomFieldInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomFieldInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomFieldInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventCustomFieldInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCustomFieldInfo withoutTrashed()
 */
	class EventCustomFieldInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCustomHtml
 *
 * @property int $id
 * @property int $event_id
 * @property string $custom_html_1
 * @property string $custom_html_2
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomHtml newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomHtml newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCustomHtml onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomHtml query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomHtml whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomHtml whereCustomHtml1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomHtml whereCustomHtml2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomHtml whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomHtml whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomHtml whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomHtml whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventCustomHtml withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCustomHtml withoutTrashed()
 */
	class EventCustomHtml extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCustomizeLabel
 *
 * @property int $id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabel newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCustomizeLabel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabel whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventCustomizeLabel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCustomizeLabel withoutTrashed()
 */
	class EventCustomizeLabel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventCustomizeLabelInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $label_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabelInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabelInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventCustomizeLabelInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabelInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabelInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabelInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabelInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabelInfo whereLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabelInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabelInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabelInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCustomizeLabelInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventCustomizeLabelInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventCustomizeLabelInfo withoutTrashed()
 */
	class EventCustomizeLabelInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventDataLogSession
 *
 * @property int $id
 * @property string $session_id
 * @property string $session_expires
 * @property string $session_data
 * @property string $delete_test
 * @property string $login_time
 * @property string $login_update
 * @property string $logout_time
 * @property string $ip_address
 * @property string $operating_system
 * @property string $device_type
 * @property string $browser_type
 * @property string $browser_version
 * @property string $user_agent
 * @property int $event_id
 * @property int $attendee_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventDataLogSession onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereBrowserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereBrowserVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereDeleteTest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereLoginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereLoginUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereLogoutTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereOperatingSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereSessionData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereSessionExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDataLogSession whereUserAgent($value)
 * @method static \Illuminate\Database\Query\Builder|EventDataLogSession withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventDataLogSession withoutTrashed()
 */
	class EventDataLogSession extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventDateFormat
 *
 * @property int $id
 * @property int $event_id
 * @property int $language_id
 * @property int $date_format_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventDateFormat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDateFormat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDateFormat query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDateFormat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDateFormat whereDateFormatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDateFormat whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDateFormat whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDateFormat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDateFormat whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDateFormat whereUpdatedAt($value)
 */
	class EventDateFormat extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventDeletionLog
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property string $url
 * @property int $attendee_count
 * @property string $soft_deleted_at
 * @property string $hard_deleted_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog whereAttendeeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog whereHardDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog whereSoftDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDeletionLog whereUrl($value)
 */
	class EventDeletionLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventDescription
 *
 * @property int $id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventDescriptionInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescription newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventDescription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescription query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescription whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventDescription withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventDescription withoutTrashed()
 */
	class EventDescription extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventDescriptionInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $description_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescriptionInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescriptionInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventDescriptionInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescriptionInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescriptionInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescriptionInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescriptionInfo whereDescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescriptionInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescriptionInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescriptionInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescriptionInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDescriptionInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventDescriptionInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventDescriptionInfo withoutTrashed()
 */
	class EventDescriptionInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventDisclaimer
 *
 * @property int $id
 * @property int $event_id
 * @property string $disclaimer
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimer newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventDisclaimer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimer query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimer whereDisclaimer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimer whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimer whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventDisclaimer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventDisclaimer withoutTrashed()
 */
	class EventDisclaimer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventDisclaimerSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $mobile_app
 * @property int $reg_site
 * @property int|null $reg_site_login
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimerSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimerSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimerSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimerSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimerSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimerSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimerSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimerSetting whereMobileApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimerSetting whereRegSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimerSetting whereRegSiteLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDisclaimerSetting whereUpdatedAt($value)
 */
	class EventDisclaimerSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventDocumentSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int|null $show_documents_notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventDocumentSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDocumentSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDocumentSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventDocumentSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDocumentSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDocumentSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDocumentSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDocumentSetting whereShowDocumentsNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventDocumentSetting whereUpdatedAt($value)
 */
	class EventDocumentSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventEmailMarketingFolder
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingFolder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingFolder newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventEmailMarketingFolder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingFolder query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingFolder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingFolder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingFolder whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingFolder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingFolder whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingFolder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventEmailMarketingFolder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventEmailMarketingFolder withoutTrashed()
 */
	class EventEmailMarketingFolder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventEmailMarketingTemplate
 *
 * @property int $id
 * @property int $event_id
 * @property int $organizer_id
 * @property string $name
 * @property string $list_type
 * @property int $folder_id
 * @property string|null $image
 * @property string|null $template
 * @property \Illuminate\Support\Carbon $created_at
 * @property int $created_by
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventEmailMarketingTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereFolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereListType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailMarketingTemplate whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|EventEmailMarketingTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventEmailMarketingTemplate withoutTrashed()
 */
	class EventEmailMarketingTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventEmailTemplate
 *
 * @property int $id
 * @property int $event_id
 * @property string $alias
 * @property string $type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmailTemplateInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventEmailTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplate whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplate whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventEmailTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventEmailTemplate withoutTrashed()
 */
	class EventEmailTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventEmailTemplateLog
 *
 * @property int $id
 * @property string $title
 * @property string $subject
 * @property string $template
 * @property int $template_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventEmailTemplateLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEmailTemplateLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventEmailTemplateLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventEmailTemplateLog withoutTrashed()
 */
	class EventEmailTemplateLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventEnded
 *
 * @property int $id
 * @property int $event_id
 * @property string $event_name
 * @property string $event_link
 * @property string $notification_date
 * @method static \Illuminate\Database\Eloquent\Builder|EventEnded newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEnded newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEnded query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEnded whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEnded whereEventLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEnded whereEventName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEnded whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEnded whereNotificationDate($value)
 */
	class EventEnded extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventEventSitePhoto
 *
 * @property int $id
 * @property int $sort_order
 * @property int $event_id
 * @property string $image
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhoto newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventEventSitePhoto onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhoto whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhoto whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhoto whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhoto whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhoto whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhoto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventEventSitePhoto withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventEventSitePhoto withoutTrashed()
 */
	class EventEventSitePhoto extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventEventSitePhotoInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $photo_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventEventSitePhotoInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo wherePhotoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventEventSitePhotoInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventEventSitePhotoInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventEventSitePhotoInfo withoutTrashed()
 */
	class EventEventSitePhotoInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventExhibitor
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property string $email
 * @property string $logo
 * @property string $booth
 * @property string|null $phone_number
 * @property string $website
 * @property string $twitter
 * @property string $facebook
 * @property string $linkedin
 * @property int $status
 * @property string $allow_reservations
 * @property int $allow_card_reader
 * @property string $login_email
 * @property string $password
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventExhibitor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereAllowCardReader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereAllowReservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereBooth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereLoginEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitor whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|EventExhibitor withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventExhibitor withoutTrashed()
 */
	class EventExhibitor extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventExhibitorAlert
 *
 * @property int $id
 * @property int $alert_id
 * @property int $exhibitor_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAlert newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorAlert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAlert whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAlert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAlert whereExhibitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAlert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAlert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorAlert withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorAlert withoutTrashed()
 */
	class EventExhibitorAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventExhibitorAttendee
 *
 * @property int $id
 * @property int $attendee_id
 * @property int $exhibitor_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAttendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorAttendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAttendee whereExhibitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorAttendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorAttendee withoutTrashed()
 */
	class EventExhibitorAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventExhibitorCategory
 *
 * @property int $id
 * @property int $exhibitor_id
 * @property int $category_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorCategory whereExhibitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorCategory withoutTrashed()
 */
	class EventExhibitorCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventExhibitorLead
 *
 * @property int $id
 * @property int $organizer_id
 * @property int $event_id
 * @property int $exhibitor_id
 * @property int $contact_person_id
 * @property int $attendee_id
 * @property string $notes
 * @property string $image_file
 * @property int $permission_allowed
 * @property string|null $rating_star
 * @property string $date_time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @property string $term_text
 * @property string $initial
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorLead onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereContactPersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereDateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereExhibitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereImageFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead wherePermissionAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereRatingStar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereTermText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventExhibitorLead whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorLead withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventExhibitorLead withoutTrashed()
 */
	class EventExhibitorLead extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventFoodAllergies
 *
 * @property int $id
 * @property int $event_id
 * @property string $subject
 * @property string $inline_text
 * @property string $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergies newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergies newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergies query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergies whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergies whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergies whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergies whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergies whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergies whereInlineText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergies whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergies whereUpdatedAt($value)
 */
	class EventFoodAllergies extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventFoodAllergiesLog
 *
 * @property int $id
 * @property int $food_id
 * @property int $event_id
 * @property string $subject
 * @property string $inline_text
 * @property string $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog whereFoodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog whereInlineText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventFoodAllergiesLog whereUpdatedAt($value)
 */
	class EventFoodAllergiesLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventGallery
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property string $image
 * @property int $status
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventGallery onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGallery whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventGallery withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventGallery withoutTrashed()
 */
	class EventGallery extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventGalleryInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $image_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventGalleryInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGalleryInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventGalleryInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventGalleryInfo withoutTrashed()
 */
	class EventGalleryInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventGdpr
 *
 * @property int $id
 * @property int $event_id
 * @property string $subject
 * @property string $inline_text
 * @property string $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdpr newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdpr newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdpr query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdpr whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdpr whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdpr whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdpr whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdpr whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdpr whereInlineText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdpr whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdpr whereUpdatedAt($value)
 */
	class EventGdpr extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventGdprLog
 *
 * @property int $id
 * @property int $gdpr_id
 * @property int $event_id
 * @property string $subject
 * @property string $inline_text
 * @property string $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog whereGdprId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog whereInlineText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprLog whereUpdatedAt($value)
 */
	class EventGdprLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventGdprSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $enable_gdpr
 * @property int $attendee_invisible
 * @property int $gdpr_required
 * @property int $auto_selected
 * @property string|null $bcc_emails
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting whereAttendeeInvisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting whereAutoSelected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting whereBccEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting whereEnableGdpr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting whereGdprRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGdprSetting whereUpdatedAt($value)
 */
	class EventGdprSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventGroup
 *
 * @property int $id
 * @property int $parent_id
 * @property string $link_type
 * @property int $event_id
 * @property string $color
 * @property int $sort_order
 * @property int $allow_multiple
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\EventGroupInfo|null $Info
 * @property-read \Illuminate\Database\Eloquent\Collection|EventGroup[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventGroupInfo[] $childrenInfo
 * @property-read int|null $children_info_count
 * @property-read EventGroup $parent
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup whereAllowMultiple($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventGroup withoutTrashed()
 */
	class EventGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventGroupInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property string $end_date
 * @property int $languages_id
 * @property int $group_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventGroupInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventGroupInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventGroupInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventGroupInfo withoutTrashed()
 */
	class EventGroupInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventHotel
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property int $rooms
 * @property float $price
 * @property float $vat
 * @property string $price_type
 * @property int $max_rooms
 * @property string|null $hotel_from_date
 * @property string|null $hotel_to_date
 * @property int $sort_order
 * @property int $status
 * @property int $is_archive
 * @property int $new_imp_flag
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventHotelInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventHotelRoom[] $room
 * @property-read int|null $room_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventHotel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereHotelFromDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereHotelToDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereIsArchive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereMaxRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereNewImpFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel wherePriceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotel whereVat($value)
 * @method static \Illuminate\Database\Query\Builder|EventHotel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventHotel withoutTrashed()
 */
	class EventHotel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventHotelInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $hotel_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventHotelInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventHotelInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventHotelInfo withoutTrashed()
 */
	class EventHotelInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventHotelPerson
 *
 * @property int $id
 * @property int|null $order_hotel_id
 * @property int $order_id
 * @property int $hotel_id
 * @property string|null $name
 * @property int|null $dob
 * @property int|null $room_no
 * @property int|null $attendee_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventHotelPerson onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson whereOrderHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson whereRoomNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelPerson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventHotelPerson withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventHotelPerson withoutTrashed()
 */
	class EventHotelPerson extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventHotelRoom
 *
 * @property int $id
 * @property int $hotel_id
 * @property string $available_date
 * @property int $total_rooms
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelRoom newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventHotelRoom onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelRoom whereAvailableDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelRoom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelRoom whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelRoom whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelRoom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelRoom whereTotalRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHotelRoom whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventHotelRoom withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventHotelRoom withoutTrashed()
 */
	class EventHotelRoom extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $event_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfo whereValue($value)
 */
	class EventInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventInfoMenu
 *
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property int $sort_order
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventInfoMenuInfo[] $Info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventInfoMenu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventInfoMenu withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventInfoMenu withoutTrashed()
 */
	class EventInfoMenu extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventInfoMenuInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $menu_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventInfoMenuInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoMenuInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventInfoMenuInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventInfoMenuInfo withoutTrashed()
 */
	class EventInfoMenuInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventInfoPage
 *
 * @property int $id
 * @property int $sort_order
 * @property int $menu_id
 * @property int $event_id
 * @property int $page_type 1=cms page; 2=url
 * @property string $image
 * @property string $image_position
 * @property string $pdf
 * @property string $icon
 * @property string $url
 * @property string $website_protocol
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventInfoPageInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventInfoPage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereImagePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage wherePageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage wherePdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPage whereWebsiteProtocol($value)
 * @method static \Illuminate\Database\Query\Builder|EventInfoPage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventInfoPage withoutTrashed()
 */
	class EventInfoPage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventInfoPageInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $page_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventInfoPageInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventInfoPageInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventInfoPageInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventInfoPageInfo withoutTrashed()
 */
	class EventInfoPageInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventLanguage
 *
 * @property int $id
 * @property int $event_id
 * @property int $language_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventLanguage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventLanguage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventLanguage query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventLanguage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLanguage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLanguage whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLanguage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLanguage whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLanguage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLanguage whereUpdatedAt($value)
 */
	class EventLanguage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventLead
 *
 * @property int $id
 * @property int $event_id
 * @property string $device_id
 * @property int $contact_person_id
 * @property int $type_id
 * @property string $contact_person_type
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property int $rating
 * @property string $raw_data
 * @property string|null $image_file
 * @property string|null $initial
 * @property int $permission_allowed
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $term_text
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventLead onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereContactPersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereContactPersonType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereImageFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead wherePermissionAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereRawData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereTermText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLead whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventLead withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventLead withoutTrashed()
 */
	class EventLead extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventLike
 *
 * @property int $id
 * @property int $image_id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventLike newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventLike onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventLike query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventLike whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLike whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLike whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLike whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLike whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventLike withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventLike withoutTrashed()
 */
	class EventLike extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventMap
 *
 * @property int $id
 * @property int $event_id
 * @property int|null $google_map
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MapInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventMap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMap newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventMap onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMap query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMap whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMap whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMap whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMap whereGoogleMap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMap whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMap whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMap whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventMap withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventMap withoutTrashed()
 */
	class EventMap extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventMapLabel
 *
 * @property int $id
 * @property int $event_id
 * @property int $label_id
 * @property int $language_id
 * @property string $value
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapLabel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapLabel whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapLabel whereLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapLabel whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapLabel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapLabel whereValue($value)
 */
	class EventMapLabel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventMapSetting
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property string $value
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapSetting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMapSetting whereValue($value)
 */
	class EventMapSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventMeetingHistory
 *
 * @method static \Illuminate\Database\Eloquent\Builder|EventMeetingHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMeetingHistory newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventMeetingHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMeetingHistory query()
 * @method static \Illuminate\Database\Query\Builder|EventMeetingHistory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventMeetingHistory withoutTrashed()
 */
	class EventMeetingHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventMessage
 *
 * @property int $mid
 * @property int $event_id
 * @property int $group_id
 * @property int $seq
 * @property string $created_on
 * @property int $created_by
 * @property string $subject
 * @property string $body
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessage whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessage whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessage whereCreatedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessage whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessage whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessage whereMid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessage whereSeq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessage whereSubject($value)
 */
	class EventMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventMessageRecipient
 *
 * @property int $id
 * @property int $mid
 * @property int $seq
 * @property int $receiver
 * @property string $all_recipients
 * @property int $event_id
 * @property int $status 1=New, 2=Read,3=Delete
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient whereAllRecipients($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient whereMid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient whereReceiver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient whereSeq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMessageRecipient whereUpdatedAt($value)
 */
	class EventMessageRecipient extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventMobileApp
 *
 * @property int $id
 * @property int $event_id
 * @property int $organizer_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventMobileApp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMobileApp newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventMobileApp onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMobileApp query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventMobileApp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMobileApp whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMobileApp whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMobileApp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMobileApp whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMobileApp whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventMobileApp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventMobileApp withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventMobileApp withoutTrashed()
 */
	class EventMobileApp extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventModuleOrder
 *
 * @property int $id
 * @property int $sort_order
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $alias
 * @property string $icon
 * @property int $is_purchased
 * @property string $group
 * @property string $version
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModuleOrderInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventModuleOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereIsPurchased($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventModuleOrder whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|EventModuleOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventModuleOrder withoutTrashed()
 */
	class EventModuleOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventNameBadge
 *
 * @property int $id
 * @property string $name
 * @property int $organizer_id
 * @property int $parent_id
 * @property string $content
 * @property string $body
 * @property string $body_2
 * @property string $height
 * @property string $height_2
 * @property string $width
 * @property string $width_2
 * @property string $column
 * @property string $column_spacing
 * @property string $row_spacing
 * @property string $top
 * @property string $left
 * @property string $right
 * @property string $bottom
 * @property int $mirror
 * @property int $crop_marks
 * @property int $hide_border
 * @property int $count
 * @property int $table_badge
 * @property int $IsEmail
 * @property int $IsOrganization
 * @property int $IsJobTitle
 * @property int $IsCompanyName
 * @property int $IsDept
 * @property int $IsDelegate
 * @property int $IsTable
 * @property int $IsEventName
 * @property int $IsInitial
 * @property int $IsFirstName
 * @property int $IsLastName
 * @property int $IsIndustry
 * @property int $IsDropDown
 * @property int $IsCountry
 * @property int $IsJobTasks
 * @property int $IsInterests
 * @property int $IsNetworkGroup
 * @property int $IsWebsite
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventNameBadge onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereBody2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereBottom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereColumnSpacing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereCropMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereHeight2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereHideBorder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsDelegate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsDept($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsDropDown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsEventName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsInterests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsJobTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsNetworkGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsTable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereIsWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereMirror($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereRight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereRowSpacing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereTableBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadge whereWidth2($value)
 * @method static \Illuminate\Database\Query\Builder|EventNameBadge withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventNameBadge withoutTrashed()
 */
	class EventNameBadge extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventNameBadgeAttendee
 *
 * @property int $id
 * @property int $badge_id
 * @property string $attendee_info
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadgeAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadgeAttendee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadgeAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadgeAttendee whereAttendeeInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadgeAttendee whereBadgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNameBadgeAttendee whereId($value)
 */
	class EventNameBadgeAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventNativeAppModule
 *
 * @property int $id
 * @property int $event_id
 * @property string $module_alias
 * @property int $sort
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventNativeAppModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNativeAppModule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNativeAppModule query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNativeAppModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNativeAppModule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNativeAppModule whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNativeAppModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNativeAppModule whereModuleAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNativeAppModule whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNativeAppModule whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNativeAppModule whereUpdatedAt($value)
 */
	class EventNativeAppModule extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventNotification
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property string $type
 * @property string $link
 * @property int $status
 * @property string $date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNotification whereUpdatedAt($value)
 */
	class EventNotification extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventOrderHotel
 *
 * @property int $id
 * @property int $hotel_id
 * @property int $order_id
 * @property string $name
 * @property float $price
 * @property string $price_type
 * @property float $vat
 * @property float|null $vat_price
 * @property int|null $rooms
 * @property string $checkin
 * @property string $checkout
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventOrderHotel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereCheckin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereCheckout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel wherePriceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventOrderHotel whereVatPrice($value)
 * @method static \Illuminate\Database\Query\Builder|EventOrderHotel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventOrderHotel withoutTrashed()
 */
	class EventOrderHotel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventPoll
 *
 * @property int $id
 * @property int $event_id
 * @property int $sort_order
 * @property int $is_anonymous
 * @property int $agenda_id
 * @property string $start_date
 * @property string $end_date
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventPoll onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll whereIsAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPoll whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventPoll withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventPoll withoutTrashed()
 */
	class EventPoll extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventPollAlert
 *
 * @property int $id
 * @property int $alert_id
 * @property int $poll_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAlert newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventPollAlert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAlert whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAlert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAlert wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAlert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAlert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventPollAlert withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventPollAlert withoutTrashed()
 */
	class EventPollAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventPollAnswer
 *
 * @property int $id
 * @property int $correct
 * @property int $question_id
 * @property int $status
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswer newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventPollAnswer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswer whereCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswer whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventPollAnswer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventPollAnswer withoutTrashed()
 */
	class EventPollAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventPollAnswerInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $answer_id
 * @property int $question_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventPollAnswerInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollAnswerInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventPollAnswerInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventPollAnswerInfo withoutTrashed()
 */
	class EventPollAnswerInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventPollGroup
 *
 * @property int $id
 * @property int $poll_id
 * @property int $group_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollGroup wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollGroup whereUpdatedAt($value)
 */
	class EventPollGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventPollMatrix
 *
 * @property int $id
 * @property string $name
 * @property int $sort_order
 * @property int $question_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollMatrix newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollMatrix newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventPollMatrix onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollMatrix query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollMatrix whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollMatrix whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollMatrix whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollMatrix whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollMatrix whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollMatrix whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollMatrix whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventPollMatrix withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventPollMatrix withoutTrashed()
 */
	class EventPollMatrix extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventPollQuestion
 *
 * @property int $id
 * @property string $question_type
 * @property string $result_chart_type
 * @property string $required_question
 * @property string $enable_comments
 * @property int $sort_order
 * @property string $start_date
 * @property string $end_date
 * @property int $poll_id
 * @property int $status
 * @property int $max_options
 * @property int $is_anonymous
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventPollQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereEnableComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereIsAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereMaxOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereQuestionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereRequiredQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereResultChartType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventPollQuestion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventPollQuestion withoutTrashed()
 */
	class EventPollQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventPollResult
 *
 * @property int $id
 * @property string $answer
 * @property string $comments
 * @property int $question_id
 * @property int $answer_id
 * @property int $event_id
 * @property int $poll_id
 * @property int $agenda_id
 * @property int $attendee_id
 * @property int $is_updated
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventPollResult onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereIsUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventPollResult withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventPollResult withoutTrashed()
 */
	class EventPollResult extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventPollResultScore
 *
 * @property int $id
 * @property int $score
 * @property int $event_id
 * @property int $question_id
 * @property int $attendee_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventPollResultScore onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventPollResultScore whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventPollResultScore withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventPollResultScore withoutTrashed()
 */
	class EventPollResultScore extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventReportingAgent
 *
 * @property int $id
 * @property int $event_id
 * @property int $reporting_agent_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventReportingAgent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventReportingAgent newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventReportingAgent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventReportingAgent query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventReportingAgent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventReportingAgent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventReportingAgent whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventReportingAgent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventReportingAgent whereReportingAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventReportingAgent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventReportingAgent withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventReportingAgent withoutTrashed()
 */
	class EventReportingAgent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSaleAgent
 *
 * @property int $id
 * @property int $event_id
 * @property int $sale_agent_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSaleAgent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSaleAgent newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSaleAgent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSaleAgent query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSaleAgent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSaleAgent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSaleAgent whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSaleAgent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSaleAgent whereSaleAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSaleAgent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSaleAgent withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSaleAgent withoutTrashed()
 */
	class EventSaleAgent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSetting
 *
 * @property int $id
 * @property string $name
 * @property string|null $value
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSetting whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSetting withoutTrashed()
 */
	class EventSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventShareTemplate
 *
 * @property int $id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventShareTemplateInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventShareTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplate whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventShareTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventShareTemplate withoutTrashed()
 */
	class EventShareTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventShareTemplateInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $template_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventShareTemplateInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventShareTemplateInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventShareTemplateInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventShareTemplateInfo withoutTrashed()
 */
	class EventShareTemplateInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteArea
 *
 * @property int $id
 * @property string $name
 * @property string $sort_order
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteArea query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteArea whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteArea whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteArea whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteArea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteArea whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteArea whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteArea whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteArea whereUpdatedAt($value)
 */
	class EventSiteArea extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteBanner
 *
 * @property int $id
 * @property int $event_id
 * @property string $banner_type
 * @property string $video_type
 * @property string $video_duration
 * @property string $image
 * @property int $sort_order
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSiteBannerInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSiteBanner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner whereBannerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner whereVideoDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBanner whereVideoType($value)
 * @method static \Illuminate\Database\Query\Builder|EventSiteBanner withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSiteBanner withoutTrashed()
 */
	class EventSiteBanner extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteBannerInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $banner_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSiteBannerInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo whereBannerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventSiteBannerInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSiteBannerInfo withoutTrashed()
 */
	class EventSiteBannerInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteBannerSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $title
 * @property int $caption
 * @property int|null $register_button
 * @property int $bottom_bar
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting whereBottomBar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting whereRegisterButton($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteBannerSetting whereUpdatedAt($value)
 */
	class EventSiteBannerSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteDescription
 *
 * @property int $id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescription newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSiteDescription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescription query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescription whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSiteDescription withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSiteDescription withoutTrashed()
 */
	class EventSiteDescription extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteDescriptionInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $description_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescriptionInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescriptionInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSiteDescriptionInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescriptionInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescriptionInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescriptionInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescriptionInfo whereDescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescriptionInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescriptionInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescriptionInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescriptionInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteDescriptionInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventSiteDescriptionInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSiteDescriptionInfo withoutTrashed()
 */
	class EventSiteDescriptionInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteModuleOrder
 *
 * @property int $id
 * @property int $sort_order
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $alias
 * @property string $icon
 * @property int $is_purchased
 * @property string $version
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSiteModuleOrderInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSiteModuleOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder whereIsPurchased($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrder whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|EventSiteModuleOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSiteModuleOrder withoutTrashed()
 */
	class EventSiteModuleOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteModuleOrderInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $module_order_id
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSiteModuleOrderInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo whereModuleOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteModuleOrderInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventSiteModuleOrderInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSiteModuleOrderInfo withoutTrashed()
 */
	class EventSiteModuleOrderInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteSocialSection
 *
 * @property int $id
 * @property int $event_id
 * @property string $alias
 * @property string $icon
 * @property int $is_purchased
 * @property int $status
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @property string $version
 * @property-read \App\Models\EventSiteSocialSectionInfo $labels
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection whereIsPurchased($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSection whereVersion($value)
 */
	class EventSiteSocialSection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteSocialSectionInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $section_id
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSiteSocialSectionInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteSocialSectionInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventSiteSocialSectionInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSiteSocialSectionInfo withoutTrashed()
 */
	class EventSiteSocialSectionInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteText
 *
 * @property int $id
 * @property int $section_order
 * @property int $constant_order
 * @property string $alias
 * @property string $module_alias
 * @property int $event_id
 * @property int $parent_id
 * @property int $label_parent_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|EventSiteText[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSiteTextInfo[] $childrenInfo
 * @property-read int|null $children_info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSiteTextInfo[] $info
 * @property-read int|null $info_count
 * @property-read EventSiteText $parent
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSiteText onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereConstantOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereLabelParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereModuleAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereSectionOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteText whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSiteText withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSiteText withoutTrashed()
 */
	class EventSiteText extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSiteTextInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $text_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\EventSiteText $labels
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSiteTextInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo whereTextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSiteTextInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventSiteTextInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSiteTextInfo withoutTrashed()
 */
	class EventSiteTextInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSmsHistory
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $type
 * @property string $status_msg
 * @property string $sms
 * @property int $event_id
 * @property int $organizer_id
 * @property int $attendee_id
 * @property int $status
 * @property int $sent_id
 * @property string $date_sent
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSmsHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereDateSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereSentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereStatusMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSmsHistory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSmsHistory withoutTrashed()
 */
	class EventSmsHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSmsHistoryInvite
 *
 * @property int $id
 * @property int $organizer_id
 * @property int $event_id
 * @property string|null $name
 * @property string|null $email
 * @property string $phone
 * @property int $status
 * @property string $status_msg
 * @property string $sms
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereStatusMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSmsHistoryInvite whereUpdatedAt($value)
 */
	class EventSmsHistoryInvite extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSpeakerCategory
 *
 * @property int $id
 * @property int $speaker_id
 * @property int $category_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSpeakerCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerCategory whereSpeakerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSpeakerCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSpeakerCategory withoutTrashed()
 */
	class EventSpeakerCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSpeakerlistLiveLog
 *
 * @property int $id
 * @property int $event_id
 * @property int $agenda_id
 * @property int $attendee_id
 * @property string $live_date
 * @property string $start_time
 * @property string $end_time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSpeakerlistLiveLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog whereLiveDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSpeakerlistLiveLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSpeakerlistLiveLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSpeakerlistLiveLog withoutTrashed()
 */
	class EventSpeakerlistLiveLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSponsor
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property string $email
 * @property string $logo
 * @property string $booth
 * @property string|null $phone_number
 * @property string $website
 * @property string $twitter
 * @property string $facebook
 * @property string $linkedin
 * @property int $stype 1= Gold, 2= Silver
 * @property string $allow_reservations
 * @property int $status
 * @property int $allow_card_reader
 * @property string $login_email
 * @property string $password
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSponsor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereAllowCardReader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereAllowReservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereBooth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereLoginEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereStype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsor whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|EventSponsor withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSponsor withoutTrashed()
 */
	class EventSponsor extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSponsorAlert
 *
 * @property int $id
 * @property int $alert_id
 * @property int $sponsor_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAlert newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSponsorAlert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAlert whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAlert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAlert whereSponsorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAlert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAlert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSponsorAlert withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSponsorAlert withoutTrashed()
 */
	class EventSponsorAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSponsorAttendee
 *
 * @property int $id
 * @property int $attendee_id
 * @property int $sponsor_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAttendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSponsorAttendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAttendee whereSponsorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSponsorAttendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSponsorAttendee withoutTrashed()
 */
	class EventSponsorAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSponsorCategory
 *
 * @property int $id
 * @property int $sponsor_id
 * @property int $category_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSponsorCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorCategory whereSponsorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSponsorCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSponsorCategory withoutTrashed()
 */
	class EventSponsorCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSponsorLead
 *
 * @property int $id
 * @property int $organizer_id
 * @property int $event_id
 * @property int $sponsor_id
 * @property int $contact_person_id
 * @property int $attendee_id
 * @property string $notes
 * @property string $image_file
 * @property int $permission_allowed
 * @property string|null $rating_star
 * @property string $date_time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @property string $term_text
 * @property string $initial
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSponsorLead onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereContactPersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereDateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereImageFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead wherePermissionAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereRatingStar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereSponsorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereTermText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSponsorLead whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSponsorLead withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSponsorLead withoutTrashed()
 */
	class EventSponsorLead extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventStreamingChannelChat
 *
 * @property int $id
 * @property int|null $event_id
 * @property int|null $agenda_id
 * @property int|null $attendee_id
 * @property string|null $message
 * @property int|null $organizer_id
 * @property string|null $ChannelName
 * @property string|null $sendBy
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventStreamingChannelChat onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat whereChannelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat whereSendBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventStreamingChannelChat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventStreamingChannelChat withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventStreamingChannelChat withoutTrashed()
 */
	class EventStreamingChannelChat extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSubRegistration
 *
 * @property int $id
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSubRegistrationQuestion[] $question
 * @property-read int|null $question_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSubRegistrationResult[] $results
 * @property-read int|null $results_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistration newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistration onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistration query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistration whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistration whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistration whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistration withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistration withoutTrashed()
 */
	class EventSubRegistration extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSubRegistrationAnswer
 *
 * @property int $id
 * @property int $sort_order
 * @property int $question_id
 * @property int $correct
 * @property int $status
 * @property int $link_to
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSubRegistrationAnswerInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationAnswer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer whereCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer whereLinkTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationAnswer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationAnswer withoutTrashed()
 */
	class EventSubRegistrationAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSubRegistrationAnswerInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $answer_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationAnswerInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationAnswerInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationAnswerInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationAnswerInfo withoutTrashed()
 */
	class EventSubRegistrationAnswerInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSubRegistrationQuestion
 *
 * @property int $id
 * @property string $question_type
 * @property string $required_question
 * @property string $enable_comments
 * @property int $sort_order
 * @property int $sub_registration_id
 * @property int $status
 * @property string $link_to
 * @property int $max_options
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSubRegistrationAnswer[] $answer
 * @property-read int|null $answer_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSubRegistrationQuestionInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSubRegistrationResult[] $result
 * @property-read int|null $result_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereEnableComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereLinkTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereMaxOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereQuestionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereRequiredQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereSubRegistrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationQuestion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationQuestion withoutTrashed()
 */
	class EventSubRegistrationQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSubRegistrationQuestionInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $question_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationQuestionInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationQuestionInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationQuestionInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationQuestionInfo withoutTrashed()
 */
	class EventSubRegistrationQuestionInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSubRegistrationResult
 *
 * @property int $id
 * @property string $answer
 * @property int $answer_id
 * @property string|null $comments
 * @property int $event_id
 * @property int $sub_registration_id
 * @property int $question_id
 * @property int $attendee_id
 * @property string $answer_type a=after payment, b= before payment
 * @property int $is_updated
 * @property int $update_itration
 * @property int $result_clear_admin
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationResult onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereAnswerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereIsUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereResultClearAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereSubRegistrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereUpdateItration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationResult withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSubRegistrationResult withoutTrashed()
 */
	class EventSubRegistrationResult extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSubRegistrationSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $listing
 * @property int $answer
 * @property int $link_to
 * @property int $show_optional
 * @property int $update_answer_email
 * @property int $result_email
 * @property string|null $end_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereLinkTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereListing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereResultEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereShowOptional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereUpdateAnswerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSubRegistrationSetting whereUpdatedAt($value)
 */
	class EventSubRegistrationSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSurvey
 *
 * @property int $id
 * @property string $start_date
 * @property string $end_date
 * @property int $event_id
 * @property int $status
 * @property int $is_anonymous
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSurveyGroup[] $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSurveyInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TemplateCampaign[] $m_campaign
 * @property-read int|null $m_campaign_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSurveyQuestion[] $question
 * @property-read int|null $question_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSurveyResult[] $results
 * @property-read int|null $results_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSurveyResultScore[] $score
 * @property-read int|null $score_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSurveyGroup[] $surveyGroups
 * @property-read int|null $survey_groups_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSurvey onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey whereIsAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurvey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSurvey withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSurvey withoutTrashed()
 */
	class EventSurvey extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSurveyAlert
 *
 * @property int $id
 * @property int $alert_id
 * @property int $survey_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAlert newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAlert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAlert whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAlert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAlert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAlert whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAlert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAlert withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAlert withoutTrashed()
 */
	class EventSurveyAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSurveyAnswer
 *
 * @property int $id
 * @property int $sort_order
 * @property string $correct
 * @property int $question_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSurveyAnswerInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSurveyResult[] $result
 * @property-read int|null $result_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswer newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAnswer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswer whereCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswer whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAnswer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAnswer withoutTrashed()
 */
	class EventSurveyAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSurveyAnswerInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $answer_id
 * @property int $question_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAnswerInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAnswerInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAnswerInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAnswerInfo withoutTrashed()
 */
	class EventSurveyAnswerInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSurveyAttendeeResult
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $survey_id
 * @property int $question_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAttendeeResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAttendeeResult newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAttendeeResult onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAttendeeResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAttendeeResult whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAttendeeResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAttendeeResult whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAttendeeResult whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAttendeeResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAttendeeResult whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAttendeeResult whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyAttendeeResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAttendeeResult withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyAttendeeResult withoutTrashed()
 */
	class EventSurveyAttendeeResult extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSurveyGroup
 *
 * @property int $id
 * @property int $survey_id
 * @property int $group_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyGroup whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSurveyGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyGroup withoutTrashed()
 */
	class EventSurveyGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSurveyInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $languages_id
 * @property int $survey_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventSurveyInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyInfo withoutTrashed()
 */
	class EventSurveyInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSurveyMatrix
 *
 * @property int $id
 * @property string $name
 * @property int $sort_order
 * @property int $question_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyMatrix newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyMatrix newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyMatrix onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyMatrix query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyMatrix whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyMatrix whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyMatrix whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyMatrix whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyMatrix whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyMatrix whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyMatrix whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSurveyMatrix withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyMatrix withoutTrashed()
 */
	class EventSurveyMatrix extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSurveyQuestion
 *
 * @property int $id
 * @property string $question_type
 * @property string $result_chart_type
 * @property string $anonymous
 * @property string $required_question
 * @property string $enable_comments
 * @property int $sort_order
 * @property string $start_date
 * @property string $end_date
 * @property int $survey_id
 * @property int $status
 * @property int $is_anonymous
 * @property int $max_options
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSurveyAnswer[] $answer
 * @property-read int|null $answer_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SurveyQuestionInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventSurveyMatrix[] $matrix
 * @property-read int|null $matrix_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereEnableComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereIsAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereMaxOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereQuestionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereRequiredQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereResultChartType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSurveyQuestion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyQuestion withoutTrashed()
 */
	class EventSurveyQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSurveyResult
 *
 * @property int $id
 * @property string $answer
 * @property string $comment
 * @property int $event_id
 * @property int $survey_id
 * @property int $question_id
 * @property int $answer_id
 * @property int $attendee_id
 * @property int $status
 * @property int $is_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyResult onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereIsUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSurveyResult withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyResult withoutTrashed()
 */
	class EventSurveyResult extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSurveyResultScore
 *
 * @property int $id
 * @property string $score
 * @property int $survey_id
 * @property int $attendee_id
 * @property int $event_id
 * @property int $question_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyResultScore onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSurveyResultScore whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventSurveyResultScore withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventSurveyResultScore withoutTrashed()
 */
	class EventSurveyResultScore extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventTicket
 *
 * @property int $id
 * @property string $serial
 * @property int $event_id
 * @property int $addon_id
 * @property int $ticket_item_id
 * @property string|null $addon_type
 * @property string|null $qr_string
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket whereAddonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket whereAddonType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket whereQrString($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket whereSerial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket whereTicketItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventTicket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventTicket withoutTrashed()
 */
	class EventTicket extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventTicketItemConfig
 *
 * @property int $id
 * @property int $event_id
 * @property int|null $ticket_item_id
 * @property string|null $serial_start
 * @property string|null $prefix
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemConfig newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventTicketItemConfig onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemConfig whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemConfig whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemConfig wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemConfig whereSerialStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemConfig whereTicketItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventTicketItemConfig withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventTicketItemConfig withoutTrashed()
 */
	class EventTicketItemConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventTicketItemValidity
 *
 * @property int $id
 * @property int $ticket_item_id
 * @property string $valid_from
 * @property string $valid_to
 * @property int $usage_limit
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemValidity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemValidity newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventTicketItemValidity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemValidity query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemValidity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemValidity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemValidity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemValidity whereTicketItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemValidity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemValidity whereUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemValidity whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketItemValidity whereValidTo($value)
 * @method static \Illuminate\Database\Query\Builder|EventTicketItemValidity withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventTicketItemValidity withoutTrashed()
 */
	class EventTicketItemValidity extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventTicketSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int|null $show_price
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketSetting whereShowPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketSetting whereUpdatedAt($value)
 */
	class EventTicketSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventTicketUsageHistory
 *
 * @property int $id
 * @property int $ticket_id
 * @property int|null $used_by
 * @property string $used_on
 * @property int $checked_by
 * @property int|null $is_organizer
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketUsageHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketUsageHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketUsageHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketUsageHistory whereCheckedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketUsageHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketUsageHistory whereIsOrganizer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketUsageHistory whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketUsageHistory whereUsedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketUsageHistory whereUsedOn($value)
 */
	class EventTicketUsageHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventTicketValidity
 *
 * @property int $id
 * @property int $ticket_id
 * @property string $valid_from
 * @property string $valid_to
 * @property int|null $usage_limit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketValidity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketValidity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketValidity query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketValidity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketValidity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketValidity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketValidity whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketValidity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketValidity whereUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketValidity whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTicketValidity whereValidTo($value)
 */
	class EventTicketValidity extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventTrack
 *
 * @property int $id
 * @property int $parent_id
 * @property int $sort_order
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TrackInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventAgenda[] $programs
 * @property-read int|null $programs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|EventTrack[] $sub_tracks
 * @property-read int|null $sub_tracks_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrack newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventTrack onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrack query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrack whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrack whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrack whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrack whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrack whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrack whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventTrack withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventTrack withoutTrashed()
 */
	class EventTrack extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventTrackIdRequest
 *
 * @property int $id
 * @property int $event_id
 * @property int $organizer_id
 * @property int $status
 * @property int $read
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrackIdRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrackIdRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrackIdRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrackIdRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrackIdRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrackIdRequest whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrackIdRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrackIdRequest whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrackIdRequest whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrackIdRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTrackIdRequest whereUpdatedAt($value)
 */
	class EventTrackIdRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventTurnListSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $status
 * @property int $turnlist_attendee_approval
 * @property int $enable_speech_time
 * @property int $enable_speech_time_for_moderator
 * @property int $display_time
 * @property int $show_image_turnlist
 * @property int $show_company_turnlist
 * @property int $show_title_turnlist
 * @property int $show_awaiting_turnlist
 * @property int $show_delegate_turnlist
 * @property int $show_department_turnlist
 * @property int $show_program_section
 * @property int $speak_time
 * @property int $turn_project_refresh_time
 * @property string $delegate_label
 * @property string $department_label
 * @property int $time_between_attendees
 * @property string $background_image
 * @property string $background_color
 * @property string|null $headings_color
 * @property string|null $description_color
 * @property string|null $program_section_color
 * @property float|null $font_size
 * @property string $text_color1
 * @property string $text_color2
 * @property string $text_color3
 * @property int $organizer_info
 * @property int $ask_to_apeak
 * @property string|null $av_output_all_template
 * @property string|null $av_output_active_template
 * @property string|null $av_output_sub_active_template
 * @property string|null $av_output_next_template
 * @property string|null $av_output_count_template
 * @property string|null $active_bg_color
 * @property string|null $all_bg_color
 * @property string|null $count_bg_color
 * @property string|null $live_attendee_detail_bg_color
 * @property string|null $speaking_now_background_color
 * @property string|null $speaking_now_text_color
 * @property string|null $attendee_detail_background_color
 * @property string|null $program_detail_background_color
 * @property int|null $show_network_group_turnlist
 * @property string|null $lobby_url
 * @property string|null $lobby_name
 * @property string|null $network_label
 * @property int|null $ask_to_speak_notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventTurnListSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereActiveBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereAllBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereAskToApeak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereAskToSpeakNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereAttendeeDetailBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereAvOutputActiveTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereAvOutputAllTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereAvOutputCountTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereAvOutputNextTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereAvOutputSubActiveTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereBackgroundImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereCountBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereDelegateLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereDepartmentLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereDescriptionColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereDisplayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereEnableSpeechTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereEnableSpeechTimeForModerator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereFontSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereHeadingsColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereLiveAttendeeDetailBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereLobbyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereLobbyUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereNetworkLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereOrganizerInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereProgramDetailBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereProgramSectionColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereShowAwaitingTurnlist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereShowCompanyTurnlist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereShowDelegateTurnlist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereShowDepartmentTurnlist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereShowImageTurnlist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereShowNetworkGroupTurnlist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereShowProgramSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereShowTitleTurnlist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereSpeakTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereSpeakingNowBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereSpeakingNowTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereTextColor1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereTextColor2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereTextColor3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereTimeBetweenAttendees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereTurnProjectRefreshTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereTurnlistAttendeeApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTurnListSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventTurnListSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventTurnListSetting withoutTrashed()
 */
	class EventTurnListSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventVideo
 *
 * @property int $id
 * @property int $event_id
 * @property string $thumnail
 * @property string $URL
 * @property string $type
 * @property string $video_path
 * @property int $status
 * @property int|null $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventVideo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo whereThumnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo whereURL($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideo whereVideoPath($value)
 * @method static \Illuminate\Database\Query\Builder|EventVideo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventVideo withoutTrashed()
 */
	class EventVideo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventVideoInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $video_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventVideoInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventVideoInfo whereVideoId($value)
 * @method static \Illuminate\Database\Query\Builder|EventVideoInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventVideoInfo withoutTrashed()
 */
	class EventVideoInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventWaitingListSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $status 1=enabled; 0=disabled
 * @property int $offerletter
 * @property int $validity_duration Hours
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventWaitingListSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWaitingListSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWaitingListSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWaitingListSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWaitingListSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWaitingListSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWaitingListSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWaitingListSetting whereOfferletter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWaitingListSetting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWaitingListSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWaitingListSetting whereValidityDuration($value)
 */
	class EventWaitingListSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventWorkShopInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $workshop_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkShopInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkShopInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventWorkShopInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkShopInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkShopInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkShopInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkShopInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkShopInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkShopInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkShopInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkShopInfo whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkShopInfo whereWorkshopId($value)
 * @method static \Illuminate\Database\Query\Builder|EventWorkShopInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventWorkShopInfo withoutTrashed()
 */
	class EventWorkShopInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventWorkshop
 *
 * @property int $id
 * @property int $event_id
 * @property string $date
 * @property string $start_time
 * @property string $end_time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventWorkShopInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventAgenda[] $programs
 * @property-read int|null $programs_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshop newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventWorkshop onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshop query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshop whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshop whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshop whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshop whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventWorkshop withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventWorkshop withoutTrashed()
 */
	class EventWorkshop extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventWorkshopAlert
 *
 * @property int $id
 * @property int $alert_id
 * @property int $workshop_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshopAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshopAlert newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventWorkshopAlert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshopAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshopAlert whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshopAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshopAlert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshopAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshopAlert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshopAlert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventWorkshopAlert whereWorkshopId($value)
 * @method static \Illuminate\Database\Query\Builder|EventWorkshopAlert withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventWorkshopAlert withoutTrashed()
 */
	class EventWorkshopAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventbuizzApp
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventbuizzApp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventbuizzApp newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventbuizzApp onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventbuizzApp query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventbuizzApp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventbuizzApp whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventbuizzApp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventbuizzApp whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventbuizzApp whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventbuizzApp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventbuizzApp withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventbuizzApp withoutTrashed()
 */
	class EventbuizzApp extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventsiteBranding
 *
 * @property int $id
 * @property int $event_id
 * @property string $site_logo
 * @property string $eventsite_register_button
 * @property string $eventsite_other_buttons
 * @property int $logo_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteBranding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteBranding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteBranding query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteBranding whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteBranding whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteBranding whereEventsiteOtherButtons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteBranding whereEventsiteRegisterButton($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteBranding whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteBranding whereLogoType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteBranding whereSiteLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteBranding whereUpdatedAt($value)
 */
	class EventsiteBranding extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventsitePaymentSetting
 *
 * @property int $id
 * @property int $dibs_test
 * @property string $dibs_hmac
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @property string $eventsite_merchant_id
 * @property string $swed_bank_region
 * @property string $swed_bank_language
 * @property string $swed_bank_password
 * @property string $SecretKey
 * @property int $eventsite_currency
 * @property int $eventsite_always_apply_vat
 * @property float $eventsite_vat
 * @property string $eventsite_vat_countries comma separated ids
 * @property int $eventsite_invoice_no
 * @property string $eventsite_invoice_prefix
 * @property int $eventsite_invoice_currentnumber
 * @property string $eventsite_order_prefix
 * @property int $maintain_quantity
 * @property int $maintain_quantity_item
 * @property int $is_voucher
 * @property int $billing_merchant_type 0=DIBS,1=Your Pay
 * @property string $billing_yourpay_language
 * @property int $billing_type
 * @property int $admin_fee_status 0=hide,1=show
 * @property string $payment_terms
 * @property string $footer_text
 * @property int $invoice_dimensions
 * @property string $invoice_logo
 * @property int $eventsite_billing
 * @property int $eventsite_enable_billing_item_desc
 * @property string $bcc_emails
 * @property int $eventsite_billing_fik
 * @property int $debitor_number
 * @property int $invoice_type
 * @property int $auto_invoice
 * @property string $account_number
 * @property string $bank_name
 * @property string $payment_date
 * @property int $billing_item_type
 * @property int $eventsite_billing_detail
 * @property int $max_billing_item_quantity
 * @property int $show_business_dating
 * @property int $show_subregistration
 * @property int $eventsite_send_email_order_creator
 * @property int $evensite_additional_attendee
 * @property int $evensite_additional_company
 * @property int $evensite_additional_department
 * @property int $evensite_additional_organization
 * @property int $evensite_additional_phone
 * @property int $evensite_additional_custom_fields
 * @property int $evensite_additional_title
 * @property int $evensite_additional_last_name
 * @property int $eventsite_show_email_in_invoice
 * @property int $send_credit_note_in_email
 * @property int $show_hotels
 * @property int $show_qty_label_free
 * @property int $hotel_person
 * @property float $hotel_vat
 * @property int $hotel_vat_status
 * @property string|null $hotel_from_date
 * @property string|null $hotel_to_date
 * @property int $hotel_currency
 * @property int $show_hotel_prices
 * @property int $show_hotel_with_rooms
 * @property string $publicKey
 * @property string $privateKey
 * @property string $mistertango_markets
 * @property string $qty_from_date
 * @property int $use_qty_rules
 * @property string|null $qp_agreement_id
 * @property string|null $qp_secret_key
 * @property int|null $qp_auto_capture
 * @property string|null $wc_customer_id
 * @property string|null $wc_secret
 * @property string|null $wc_shop_id
 * @property string|null $stripe_api_key
 * @property string|null $stripe_secret_key
 * @property int $eventsite_apply_multi_vat
 * @property string|null $bambora_secret_key
 * @property int $is_item this flag use for only free event
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereAdminFeeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereAutoInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereBamboraSecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereBccEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereBillingItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereBillingMerchantType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereBillingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereBillingYourpayLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereDebitorNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereDibsHmac($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereDibsTest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEvensiteAdditionalAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEvensiteAdditionalCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEvensiteAdditionalCustomFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEvensiteAdditionalDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEvensiteAdditionalLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEvensiteAdditionalOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEvensiteAdditionalPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEvensiteAdditionalTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteAlwaysApplyVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteApplyMultiVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteBilling($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteBillingDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteBillingFik($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteEnableBillingItemDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteInvoiceCurrentnumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteInvoiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteInvoicePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteOrderPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteSendEmailOrderCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteShowEmailInInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereEventsiteVatCountries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereFooterText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereHotelCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereHotelFromDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereHotelPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereHotelToDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereHotelVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereHotelVatStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereInvoiceDimensions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereInvoiceLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereInvoiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereIsItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereIsVoucher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereMaintainQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereMaintainQuantityItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereMaxBillingItemQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereMistertangoMarkets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting wherePaymentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereQpAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereQpAutoCapture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereQpSecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereQtyFromDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereSecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereSendCreditNoteInEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereShowBusinessDating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereShowHotelPrices($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereShowHotelWithRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereShowHotels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereShowQtyLabelFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereShowSubregistration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereStripeApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereStripeSecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereSwedBankLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereSwedBankPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereSwedBankRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereUseQtyRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereWcCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereWcSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsitePaymentSetting whereWcShopId($value)
 */
	class EventsitePaymentSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventsiteSection
 *
 * @property int $id
 * @property int $event_id
 * @property string $alias
 * @property string $icon
 * @property int $is_purchased
 * @property int $status
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @property string $version
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection whereIsPurchased($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSection whereVersion($value)
 */
	class EventsiteSection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventsiteSectionInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $section_id
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventsiteSectionInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSectionInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventsiteSectionInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventsiteSectionInfo withoutTrashed()
 */
	class EventsiteSectionInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventsiteSetting
 *
 * @property int $id
 * @property int $event_id
 * @property string $ticket_left
 * @property string $registration_end_date
 * @property string $registration_end_time
 * @property string $cancellation_date
 * @property string $cancellation_end_time
 * @property string $cancellation_policy
 * @property string $registration_code
 * @property string $mobile_phone
 * @property int $eventsite_public
 * @property int $eventsite_signup_linkedin
 * @property int $eventsite_signup_fb
 * @property int $eventsite_tickets_left
 * @property int $eventsite_time_left
 * @property int $eventsite_language_menu
 * @property int $eventsite_menu
 * @property int $eventsite_banners
 * @property int $eventsite_location
 * @property int $eventsite_date
 * @property int $eventsite_footer
 * @property int $pass_changeable
 * @property int $phone_mandatory
 * @property int $attendee_registration_invite_email
 * @property int $attach_attendee_ticket
 * @property int $attendee_my_profile
 * @property int $attendee_my_program
 * @property int $attendee_my_billing
 * @property int $attendee_my_billing_history
 * @property int $attendee_my_reg_cancel
 * @property int $attendee_go_to_mbl_app
 * @property int $payment_type
 * @property int $use_waitinglist
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $goto_eventsite
 * @property int $eventsite_add_calender
 * @property int $registration_after_login
 * @property int $send_invoice_email
 * @property int $attach_invoice_email
 * @property int $attach_calendar_to_email
 * @property int $auto_complete
 * @property int $new_message_temp
 * @property int $go_to_account
 * @property int $go_to_home_page
 * @property int $attendee_my_sub_registration
 * @property int $third_party_redirect
 * @property int $agenda_search_filter
 * @property string|null $third_party_redirect_url
 * @property int $attach_my_program
 * @property int $quick_register
 * @property int $prefill_reg_form
 * @property int $search_engine_visibility
 * @property int|null $attach_invoice_email_online_payment
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventsiteSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAgendaSearchFilter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttachAttendeeTicket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttachCalendarToEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttachInvoiceEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttachInvoiceEmailOnlinePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttachMyProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttendeeGoToMblApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttendeeMyBilling($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttendeeMyBillingHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttendeeMyProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttendeeMyProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttendeeMyRegCancel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttendeeMySubRegistration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAttendeeRegistrationInviteEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereAutoComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereCancellationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereCancellationEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereCancellationPolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsiteAddCalender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsiteBanners($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsiteDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsiteFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsiteLanguageMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsiteLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsiteMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsitePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsiteSignupFb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsiteSignupLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsiteTicketsLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereEventsiteTimeLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereGoToAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereGoToHomePage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereGotoEventsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereMobilePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereNewMessageTemp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting wherePassChangeable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting wherePhoneMandatory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting wherePrefillRegForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereQuickRegister($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereRegistrationAfterLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereRegistrationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereRegistrationEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereRegistrationEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereSearchEngineVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereSendInvoiceEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereThirdPartyRedirect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereThirdPartyRedirectUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereTicketLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteSetting whereUseWaitinglist($value)
 * @method static \Illuminate\Database\Query\Builder|EventsiteSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventsiteSetting withoutTrashed()
 */
	class EventsiteSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventsiteStreaming
 *
 * @property int $id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreaming newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreaming newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventsiteStreaming onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreaming query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreaming whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreaming whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreaming whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreaming whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreaming whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventsiteStreaming withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventsiteStreaming withoutTrashed()
 */
	class EventsiteStreaming extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventsiteStreamingInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $stream_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreamingInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreamingInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventsiteStreamingInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreamingInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreamingInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreamingInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreamingInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreamingInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreamingInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreamingInfo whereStreamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreamingInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventsiteStreamingInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|EventsiteStreamingInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventsiteStreamingInfo withoutTrashed()
 */
	class EventsiteStreamingInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExhibitorAttendee
 *
 * @property int $id
 * @property int $exhibitor_id
 * @property int $attendee_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorAttendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|ExhibitorAttendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorAttendee whereExhibitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ExhibitorAttendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ExhibitorAttendee withoutTrashed()
 */
	class ExhibitorAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExhibitorInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $exhibitor_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|ExhibitorInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo whereExhibitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|ExhibitorInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ExhibitorInfo withoutTrashed()
 */
	class ExhibitorInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExhibitorNote
 *
 * @property int $id
 * @property int $event_id
 * @property string $notes
 * @property int $attendee_id
 * @property int $exhibitor_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorNote newQuery()
 * @method static \Illuminate\Database\Query\Builder|ExhibitorNote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorNote whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorNote whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorNote whereExhibitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorNote whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ExhibitorNote withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ExhibitorNote withoutTrashed()
 */
	class ExhibitorNote extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExhibitorSetting
 *
 * @property int $id
 * @property int $event_id
 * @property string $exhibitor_list
 * @property int $exhibitorName
 * @property int $exhibitorPhone
 * @property int $exhibitorEmail
 * @property int $contact_person_email
 * @property int $exhibitorContact
 * @property int $exhibitorTab
 * @property int $catTab
 * @property int $sortType
 * @property int $hide_attendee
 * @property int $mark_favorite
 * @property int $poll
 * @property int $document
 * @property int $reservation
 * @property int $reservation_type
 * @property int $reservation_req_type_email
 * @property int $reservation_req_type_sms
 * @property int $reservation_allow_contact_person
 * @property int $reservation_allow_multiple
 * @property int $auto_save
 * @property int $allow_card_reader
 * @property int $show_contact_person
 * @property int $gdpr_accepted
 * @property int $recieve_lead_email_on_save
 * @property int $show_booth
 * @property int $notes
 * @property string|null $bcc_emails
 * @property int $show_lead_email_button
 * @property int $reservation_icone_view
 * @property int $reservations_overview
 * @property int $reservation_overview_icone
 * @property int $reservations_view
 * @property int $reservation_display_filters
 * @property int $reservation_time_slots
 * @property int $reservation_available_meeting_rooms
 * @property int $reservation_meeting_rooms
 * @property int $reservation_display_colleagues
 * @property int $reservation_display_company
 * @property int $colleague_book_meeting
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|ExhibitorSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereAllowCardReader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereAutoSave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereBccEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereCatTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereColleagueBookMeeting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereContactPersonEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereExhibitorContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereExhibitorEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereExhibitorList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereExhibitorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereExhibitorPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereExhibitorTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereGdprAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereHideAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereMarkFavorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting wherePoll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereRecieveLeadEmailOnSave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationAllowContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationAllowMultiple($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationAvailableMeetingRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationDisplayColleagues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationDisplayCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationDisplayFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationIconeView($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationMeetingRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationOverviewIcone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationReqTypeEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationReqTypeSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationTimeSlots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationsOverview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereReservationsView($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereShowBooth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereShowContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereShowLeadEmailButton($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereSortType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitorSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ExhibitorSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ExhibitorSetting withoutTrashed()
 */
	class ExhibitorSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExportAttendeeJob
 *
 * @property int $id
 * @property int $event_id
 * @property int $key_id
 * @property string $key_name
 * @property string $model_name
 * @property string $file_name
 * @property string $email
 * @property string $ids
 * @property int $status
 * @property string|null $data
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob newQuery()
 * @method static \Illuminate\Database\Query\Builder|ExportAttendeeJob onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereKeyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereKeyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereModelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExportAttendeeJob whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ExportAttendeeJob withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ExportAttendeeJob withoutTrashed()
 */
	class ExportAttendeeJob extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FavouriteAttendee
 *
 * @property int $id
 * @property int $attendee_id
 * @property int $fovirate_attendee_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FavouriteAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FavouriteAttendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|FavouriteAttendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FavouriteAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|FavouriteAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FavouriteAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FavouriteAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FavouriteAttendee whereFovirateAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FavouriteAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FavouriteAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|FavouriteAttendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FavouriteAttendee withoutTrashed()
 */
	class FavouriteAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Field
 *
 * @property int $id
 * @property int $sort_order
 * @property int $status
 * @property int $mandatory
 * @property string $field_alias
 * @property string $type
 * @property string $section_alias
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillingFieldInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|Field newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Field newQuery()
 * @method static \Illuminate\Database\Query\Builder|Field onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Field query()
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereFieldAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereMandatory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereSectionAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Field withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Field withoutTrashed()
 */
	class Field extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FieldInfo
 *
 * @property int $id
 * @property int $field_id
 * @property string $name
 * @property string $value
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FieldInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|FieldInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldInfo whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|FieldInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FieldInfo withoutTrashed()
 */
	class FieldInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FileInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $file_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|FileInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|FileInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FileInfo withoutTrashed()
 */
	class FileInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FloorPlan
 *
 * @property int $id
 * @property string $document
 * @property string $image
 * @property int $event_id
 * @property int $organizer_id
 * @property int $status
 * @property string $pins_data
 * @property int $read
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan newQuery()
 * @method static \Illuminate\Database\Query\Builder|FloorPlan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan whereDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan wherePinsData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FloorPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|FloorPlan withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FloorPlan withoutTrashed()
 */
	class FloorPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FoodAllergiesAttendeeLog
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $food_accept
 * @property string $food_description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FoodAllergiesAttendeeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodAllergiesAttendeeLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|FoodAllergiesAttendeeLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodAllergiesAttendeeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodAllergiesAttendeeLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodAllergiesAttendeeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodAllergiesAttendeeLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodAllergiesAttendeeLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodAllergiesAttendeeLog whereFoodAccept($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodAllergiesAttendeeLog whereFoodDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodAllergiesAttendeeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodAllergiesAttendeeLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|FoodAllergiesAttendeeLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FoodAllergiesAttendeeLog withoutTrashed()
 */
	class FoodAllergiesAttendeeLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GdprAttendeeLog
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $gdpr_accept
 * @property string|null $gdpr_description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GdprAttendeeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GdprAttendeeLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|GdprAttendeeLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GdprAttendeeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|GdprAttendeeLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GdprAttendeeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GdprAttendeeLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GdprAttendeeLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GdprAttendeeLog whereGdprAccept($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GdprAttendeeLog whereGdprDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GdprAttendeeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GdprAttendeeLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GdprAttendeeLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GdprAttendeeLog withoutTrashed()
 */
	class GdprAttendeeLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GeneralInfoMenu
 *
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property int $sort_order
 * @property int $event_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GeneralInfoMenuInfo[] $Info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu newQuery()
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoMenu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoMenu withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoMenu withoutTrashed()
 */
	class GeneralInfoMenu extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GeneralInfoMenuInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $menu_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoMenuInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoMenuInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoMenuInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoMenuInfo withoutTrashed()
 */
	class GeneralInfoMenuInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GeneralInfoPage
 *
 * @property int $id
 * @property int $sort_order
 * @property int $menu_id
 * @property int $event_id
 * @property int $page_type 1=cms page; 2=url
 * @property string $image
 * @property string $image_position
 * @property string $pdf
 * @property string $icon
 * @property string $url
 * @property string $website_protocol
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GeneralInfoPageInfo[] $info
 * @property-read int|null $info_count
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage newQuery()
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoPage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage query()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereImagePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage wherePageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage wherePdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPage whereWebsiteProtocol($value)
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoPage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoPage withoutTrashed()
 */
	class GeneralInfoPage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GeneralInfoPageInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $page_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoPageInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GeneralInfoPageInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoPageInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GeneralInfoPageInfo withoutTrashed()
 */
	class GeneralInfoPageInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\HubAdminEvent
 *
 * @property int $id
 * @property int $hub_admin_id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdminEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdminEvent newQuery()
 * @method static \Illuminate\Database\Query\Builder|HubAdminEvent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdminEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdminEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdminEvent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdminEvent whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdminEvent whereHubAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdminEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdminEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|HubAdminEvent withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HubAdminEvent withoutTrashed()
 */
	class HubAdminEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\HubAdministrator
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator newQuery()
 * @method static \Illuminate\Database\Query\Builder|HubAdministrator onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator query()
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HubAdministrator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|HubAdministrator withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HubAdministrator withoutTrashed()
 */
	class HubAdministrator extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Integration
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organizer[] $organizers
 * @property-read int|null $organizers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\IntegrationRule[] $rules
 * @property-read int|null $rules_count
 * @method static \Illuminate\Database\Eloquent\Builder|Integration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Integration newQuery()
 * @method static \Illuminate\Database\Query\Builder|Integration onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Integration query()
 * @method static \Illuminate\Database\Query\Builder|Integration withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Integration withoutTrashed()
 */
	class Integration extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\IntegrationRule
 *
 * @property-read IntegrationRule $integration
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationRule newQuery()
 * @method static \Illuminate\Database\Query\Builder|IntegrationRule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationRule query()
 * @method static \Illuminate\Database\Query\Builder|IntegrationRule withTrashed()
 * @method static \Illuminate\Database\Query\Builder|IntegrationRule withoutTrashed()
 */
	class IntegrationRule extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\InvoiceEmailReminderLog
 *
 * @property int $id
 * @property int $event_id
 * @property int $order_id
 * @property int $attendee_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceEmailReminderLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceEmailReminderLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|InvoiceEmailReminderLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceEmailReminderLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceEmailReminderLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceEmailReminderLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceEmailReminderLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceEmailReminderLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceEmailReminderLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceEmailReminderLog whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceEmailReminderLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceEmailReminderLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|InvoiceEmailReminderLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|InvoiceEmailReminderLog withoutTrashed()
 */
	class InvoiceEmailReminderLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Label
 *
 * @property int $id
 * @property int $section_order
 * @property int $constant_order
 * @property string $alias
 * @property string $module_alias
 * @property int $parent_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Label[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LabelInfo[] $childrenInfo
 * @property-read int|null $children_info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LabelInfo[] $info
 * @property-read int|null $info_count
 * @property-read Label $parent
 * @method static \Illuminate\Database\Eloquent\Builder|Label newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Label newQuery()
 * @method static \Illuminate\Database\Query\Builder|Label onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Label query()
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereConstantOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereModuleAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereSectionOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Label withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Label withoutTrashed()
 */
	class Label extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LabelInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $label_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|LabelInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo whereLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LabelInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|LabelInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LabelInfo withoutTrashed()
 */
	class LabelInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Language
 *
 * @property int $id
 * @property string $name
 * @property string $lang_code
 * @property string $ios_lang_code
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereIosLangCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereLangCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedAt($value)
 */
	class Language extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LeadTerm
 *
 * @property int $id
 * @property int $event_id
 * @property string $term_text
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTerm newQuery()
 * @method static \Illuminate\Database\Query\Builder|LeadTerm onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTerm query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTerm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTerm whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTerm whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTerm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTerm whereTermText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadTerm whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LeadTerm withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LeadTerm withoutTrashed()
 */
	class LeadTerm extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LoginDetailLog
 *
 * @property int $id
 * @property int $attendee_id
 * @property int $event_id
 * @property int $organizer_id
 * @property int $disclaimer_id
 * @property string $login_date
 * @property string $disclaimer_date
 * @property string $device
 * @property string $ip_address
 * @property string $disclaimer_version
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereDisclaimerDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereDisclaimerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereDisclaimerVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereLoginDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginDetailLog whereUpdatedAt($value)
 */
	class LoginDetailLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LoginHistory
 *
 * @property int $id
 * @property int $attendee_id
 * @property int $event_id
 * @property string $platform
 * @property string $ip
 * @property string $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @property-read \App\Models\Attendee $attendee
 * @property-read \App\Models\Attendee $attendees
 * @property-read \App\Models\Event $events
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory newQuery()
 * @method static \Illuminate\Database\Query\Builder|LoginHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory whereUserAgent($value)
 * @method static \Illuminate\Database\Query\Builder|LoginHistory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LoginHistory withoutTrashed()
 */
	class LoginHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MailingList
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $name
 * @property string|null $default_from_email
 * @property string|null $default_from_name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MailingList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingList newQuery()
 * @method static \Illuminate\Database\Query\Builder|MailingList onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingList query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingList whereDefaultFromEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingList whereDefaultFromName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingList whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingList whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingList whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|MailingList withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MailingList withoutTrashed()
 */
	class MailingList extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MailingListCampaign
 *
 * @property int $id
 * @property int $parent_id
 * @property int $organizer_id
 * @property string $subject
 * @property int $template_id
 * @property int $mailing_list_id
 * @property string $sender_name
 * @property string|null $template
 * @property string|null $status
 * @property string|null $schedule_date
 * @property string|null $schedule_time
 * @property string|null $sent_datetime
 * @property string|null $utc_datetime
 * @property string $schedule_repeat
 * @property int|null $repeat_every_qty
 * @property string|null $repeat_every_type
 * @property string|null $repeat_every_on
 * @property string|null $end_type
 * @property string|null $end_on
 * @property int|null $end_after
 * @property string|null $rss_link
 * @property int $in_progress
 * @property int $send
 * @property int $deferral
 * @property int $hard_bounce
 * @property int $soft_bounce
 * @property int $open
 * @property int $click
 * @property int $reject
 * @property int $timezone_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign newQuery()
 * @method static \Illuminate\Database\Query\Builder|MailingListCampaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereClick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereDeferral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereEndAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereEndOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereEndType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereHardBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereInProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereMailingListId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereReject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereRepeatEveryOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereRepeatEveryQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereRepeatEveryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereRssLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereScheduleDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereScheduleRepeat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereScheduleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereSend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereSenderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereSentDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereSoftBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaign whereUtcDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|MailingListCampaign withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MailingListCampaign withoutTrashed()
 */
	class MailingListCampaign extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MailingListCampaignLog
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $user_email
 * @property int $campaign_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|MailingListCampaignLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignLog whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignLog whereUserEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|MailingListCampaignLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MailingListCampaignLog withoutTrashed()
 */
	class MailingListCampaignLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MailingListCampaignRSSLog
 *
 * @property int $id
 * @property int $mailing_list_campaign_id
 * @property string $title
 * @property string|null $link
 * @property string|null $guid
 * @property string|null $pubDate
 * @property string|null $author
 * @property string|null $description
 * @property string $created_date
 * @property string $created_time
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog whereCreatedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog whereGuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog whereMailingListCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog wherePubDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListCampaignRSSLog whereTitle($value)
 */
	class MailingListCampaignRSSLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MailingListEmailMarketingTemplate
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $name
 * @property string $list_type
 * @property int $folder_id
 * @property string|null $image
 * @property string|null $template
 * @property string|null $content
 * @property \Illuminate\Support\Carbon $created_at
 * @property int $created_by
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|MailingListEmailMarketingTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereFolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereListType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListEmailMarketingTemplate whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|MailingListEmailMarketingTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MailingListEmailMarketingTemplate withoutTrashed()
 */
	class MailingListEmailMarketingTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MailingListSubscriber
 *
 * @property int $id
 * @property int $mailing_list_id
 * @property int $organizer_id
 * @property string $email
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $unsubscribed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber newQuery()
 * @method static \Illuminate\Database\Query\Builder|MailingListSubscriber onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber whereMailingListId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber whereUnsubscribed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingListSubscriber whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|MailingListSubscriber withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MailingListSubscriber withoutTrashed()
 */
	class MailingListSubscriber extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MapInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $map_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|MapInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo whereMapId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MapInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|MapInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MapInfo withoutTrashed()
 */
	class MapInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MatchMaking
 *
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property int $organizer_id
 * @property int $event_id
 * @property int $sort_order
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking newQuery()
 * @method static \Illuminate\Database\Query\Builder|MatchMaking onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking query()
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MatchMaking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|MatchMaking withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MatchMaking withoutTrashed()
 */
	class MatchMaking extends \Eloquent {}
}

namespace App\Models\Models{
/**
 * App\Models\Models\OrganizerAPNS
 *
 * @property int $id
 * @property int|null $organizer_id
 * @property string|null $key_id
 * @property string|null $team_id
 * @property string|null $apns_topic
 * @property string|null $private_key
 * @property string|null $jwt_token
 * @property string|null $issued_at UNIX timestamp in UTC timezone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerAPNS onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS whereApnsTopic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS whereIssuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS whereJwtToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS whereKeyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPNS whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrganizerAPNS withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerAPNS withoutTrashed()
 */
	class OrganizerAPNS extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Module
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string $class_name
 * @property string $group
 * @property int $sort_order
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @property string $version
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|Module newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Module newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Module query()
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereClassName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Module whereVersion($value)
 */
	class Module extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ModuleGroup
 *
 * @property int $id
 * @property int $event_id
 * @property string $alies
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|ModuleGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroup whereAlies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroup whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroup whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ModuleGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ModuleGroup withoutTrashed()
 */
	class ModuleGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ModuleGroupInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $languages_id
 * @property int $group_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroupInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroupInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroupInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroupInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroupInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroupInfo whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroupInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroupInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroupInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroupInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleGroupInfo whereValue($value)
 */
	class ModuleGroupInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ModuleOrderInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @property int $module_order_id
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo whereModuleOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleOrderInfo whereValue($value)
 */
	class ModuleOrderInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MyDocumentBccEmail
 *
 * @property int $id
 * @property int $event_id
 * @property string $bcc_email
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentBccEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentBccEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentBccEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentBccEmail whereBccEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentBccEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentBccEmail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentBccEmail whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentBccEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentBccEmail whereUpdatedAt($value)
 */
	class MyDocumentBccEmail extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MyDocumentSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee
 * @property int $attendee_group
 * @property int $program
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentSetting whereAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentSetting whereAttendeeGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentSetting whereProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyDocumentSetting whereUpdatedAt($value)
 */
	class MyDocumentSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\NativeAppUpdate
 *
 * @property int $id
 * @property int $event_id
 * @property string $last_update_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NativeAppUpdate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NativeAppUpdate newQuery()
 * @method static \Illuminate\Database\Query\Builder|NativeAppUpdate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NativeAppUpdate query()
 * @method static \Illuminate\Database\Eloquent\Builder|NativeAppUpdate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NativeAppUpdate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NativeAppUpdate whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NativeAppUpdate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NativeAppUpdate whereLastUpdateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NativeAppUpdate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NativeAppUpdate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NativeAppUpdate withoutTrashed()
 */
	class NativeAppUpdate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Organizer
 *
 * @property int $id
 * @property int $parent_id
 * @property string $first_name
 * @property string $last_name
 * @property string $user_name
 * @property string $email
 * @property string $password
 * @property string $phone
 * @property string $address
 * @property string $house_number
 * @property string $company
 * @property string $vat_number
 * @property string $zip_code
 * @property string $city
 * @property int $country
 * @property string $create_date
 * @property string $expire_date
 * @property string $domain
 * @property int $total_space
 * @property int $space_private_document
 * @property int|null $sub_admin_limit
 * @property string $status 1= Active, 2 = Pending, 3 = Expire
 * @property string $user_type
 * @property int $internal_organizer
 * @property string $legal_contact_first_name
 * @property string $export_setting
 * @property string $legal_contact_last_name
 * @property string $legal_contact_email
 * @property string $legal_contact_mobile
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $remember_token
 * @property int $show_native_app_link_all_events
 * @property int $allow_native_app
 * @property string $api_key
 * @property int $allow_api
 * @property int $allow_card_reader
 * @property int $white_label_email
 * @property int $authentication
 * @property int $authentication_type
 * @property string $authentication_code
 * @property int $email_marketing_template
 * @property int $mailing_list
 * @property string $authentication_created_date
 * @property string $license_start_date
 * @property string $license_end_date
 * @property string $license_type
 * @property int $paid
 * @property int $eventbuizz_app
 * @property int $white_label_app
 * @property int $allow_admin_access
 * @property int $allow_plug_and_play_access
 * @property string|null $language_id
 * @property string|null $last_login_ip
 * @property int $auto_renewal
 * @property int $notice_period
 * @property string $owner
 * @property string $contact_name
 * @property string $contact_email
 * @property string $notes
 * @property string $terminated_on
 * @property int $allow_nem_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer newQuery()
 * @method static \Illuminate\Database\Query\Builder|Organizer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAllowAdminAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAllowApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAllowCardReader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAllowNativeApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAllowNemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAllowPlugAndPlayAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAuthentication($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAuthenticationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAuthenticationCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAuthenticationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereAutoRenewal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereCreateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereEmailMarketingTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereEventbuizzApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereExportSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereHouseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereInternalOrganizer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereLegalContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereLegalContactFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereLegalContactLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereLegalContactMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereLicenseEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereLicenseStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereLicenseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereMailingList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereNoticePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereShowNativeAppLinkAllEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereSpacePrivateDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereSubAdminLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereTerminatedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereTotalSpace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereWhiteLabelApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereWhiteLabelEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organizer whereZipCode($value)
 * @method static \Illuminate\Database\Query\Builder|Organizer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Organizer withoutTrashed()
 */
	class Organizer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrganizerAPIRequestLog
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $api_key
 * @property string $request_type
 * @property string $request_responce
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPIRequestLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPIRequestLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerAPIRequestLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPIRequestLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPIRequestLog whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPIRequestLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPIRequestLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPIRequestLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPIRequestLog whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPIRequestLog whereRequestResponce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPIRequestLog whereRequestType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerAPIRequestLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrganizerAPIRequestLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerAPIRequestLog withoutTrashed()
 */
	class OrganizerAPIRequestLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrganizerCalendarApiRequest
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $api_key
 * @property string $request_date
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $user_IP
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerCalendarApiRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest whereRequestDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerCalendarApiRequest whereUserIP($value)
 * @method static \Illuminate\Database\Query\Builder|OrganizerCalendarApiRequest withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerCalendarApiRequest withoutTrashed()
 */
	class OrganizerCalendarApiRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrganizerIntegrationCredential
 *
 * @property-read \App\Models\Organizer $organizer
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerIntegrationCredential newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerIntegrationCredential newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerIntegrationCredential onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerIntegrationCredential query()
 * @method static \Illuminate\Database\Query\Builder|OrganizerIntegrationCredential withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerIntegrationCredential withoutTrashed()
 */
	class OrganizerIntegrationCredential extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrganizerMediaLibrary
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $file_name
 * @property string $type
 * @property string|null $original_filename
 * @property int|null $size
 * @property int|null $weight
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerMediaLibrary onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary whereOriginalFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerMediaLibrary whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|OrganizerMediaLibrary withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerMediaLibrary withoutTrashed()
 */
	class OrganizerMediaLibrary extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrganizerPermission
 *
 * @property int $id
 * @property string $module_name
 * @property string $permissions_name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerPermission newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerPermission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerPermission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerPermission whereModuleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerPermission wherePermissionsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerPermission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrganizerPermission withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerPermission withoutTrashed()
 */
	class OrganizerPermission extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrganizerSiteBanner
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $image
 * @property string|null $title
 * @property string|null $caption
 * @property int $sort_order
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteBanner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBanner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteBanner withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteBanner withoutTrashed()
 */
	class OrganizerSiteBanner extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrganizerSiteBannerSetting
 *
 * @property int $id
 * @property int $organizer_id
 * @property int|null $title
 * @property int|null $caption
 * @property int|null $register_button
 * @property int|null $bottom_bar
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteBannerSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting whereBottomBar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting whereRegisterButton($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteBannerSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteBannerSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteBannerSetting withoutTrashed()
 */
	class OrganizerSiteBannerSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrganizerSiteLabel
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $alias
 * @property int $parent_id
 * @property int $languages_id
 * @property string $value
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteLabel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabel whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteLabel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteLabel withoutTrashed()
 */
	class OrganizerSiteLabel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrganizerSiteLabelMaster
 *
 * @property int $id
 * @property string $alias
 * @property string $value
 * @property int $parent_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabelMaster newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabelMaster newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteLabelMaster onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabelMaster query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabelMaster whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabelMaster whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabelMaster whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabelMaster whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabelMaster whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabelMaster whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabelMaster whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteLabelMaster whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteLabelMaster withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteLabelMaster withoutTrashed()
 */
	class OrganizerSiteLabelMaster extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrganizerSiteSetting
 *
 * @property int $id
 * @property int $organizer_id
 * @property string|null $logo
 * @property int|null $show_banner
 * @property string|null $aboutus
 * @property string|null $contactus
 * @property int $status
 * @property string $primary_color
 * @property string $secondary_color
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting whereAboutus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting whereContactus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting whereShowBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerSiteSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerSiteSetting withoutTrashed()
 */
	class OrganizerSiteSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrganizerUserPermission
 *
 * @property int $id
 * @property int $organizer_user_id
 * @property int $permission_id
 * @property int $add_permissions
 * @property int $edit_permissions
 * @property int $delete_permissions
 * @property int $view_permissions
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrganizerUserPermission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission whereAddPermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission whereDeletePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission whereEditPermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission whereOrganizerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizerUserPermission whereViewPermissions($value)
 * @method static \Illuminate\Database\Query\Builder|OrganizerUserPermission withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrganizerUserPermission withoutTrashed()
 */
	class OrganizerUserPermission extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Package
 *
 * @property int $id
 * @property int|null $admin_id
 * @property string $name
 * @property string $description
 * @property string $no_of_event
 * @property int $expire_duration
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $total_attendees
 * @property int $registration_site_check
 * @method static \Illuminate\Database\Eloquent\Builder|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package newQuery()
 * @method static \Illuminate\Database\Query\Builder|Package onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereExpireDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereNoOfEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereRegistrationSiteCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereTotalAttendees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Package withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Package withoutTrashed()
 */
	class Package extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PackageDetail
 *
 * @property int $id
 * @property int $package_id
 * @property int $addons_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDetail newQuery()
 * @method static \Illuminate\Database\Query\Builder|PackageDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDetail whereAddonsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDetail wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PackageDetail withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PackageDetail withoutTrashed()
 */
	class PackageDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PackagePayment
 *
 * @property int $id
 * @property int $admin_id
 * @property int $customer_agent_id
 * @property int $organizer_id
 * @property int $assign_package_id
 * @property string $invoice
 * @property int $amount
 * @property string $invoice_date
 * @property int $sale_agent_id
 * @property string $contact_person
 * @property string $contact_person_email
 * @property string $contact_person_mobile
 * @property string $im_type
 * @property string $im_id
 * @property string $first_contact_date
 * @property string $traning_session_date
 * @property string $description
 * @property string $currency
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment newQuery()
 * @method static \Illuminate\Database\Query\Builder|PackagePayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereAssignPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereContactPersonEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereContactPersonMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereCustomerAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereFirstContactDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereImId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereImType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereInvoiceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereSaleAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereTraningSessionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackagePayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PackagePayment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PackagePayment withoutTrashed()
 */
	class PackagePayment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Partner
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $p_name
 * @property string $p_key
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Partner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner newQuery()
 * @method static \Illuminate\Database\Query\Builder|Partner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner wherePKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner wherePName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Partner withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Partner withoutTrashed()
 */
	class Partner extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PasswordReset
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset query()
 */
	class PasswordReset extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $payment_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentInfo whereValue($value)
 */
	class PaymentInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PlugnplayModulesProgress
 *
 * @property int $id
 * @property string|null $module
 * @property int|null $status
 * @property int|null $event_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PlugnplayModulesProgress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlugnplayModulesProgress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlugnplayModulesProgress query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlugnplayModulesProgress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlugnplayModulesProgress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlugnplayModulesProgress whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlugnplayModulesProgress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlugnplayModulesProgress whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlugnplayModulesProgress whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlugnplayModulesProgress whereUpdatedAt($value)
 */
	class PlugnplayModulesProgress extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PollLog
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $poll_id
 * @property int $status 1=Rendered,2=Submitted
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|PollLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PollLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PollLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|PollLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollLog wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollLog whereStatus($value)
 */
	class PollLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PollQuestionInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $question_id
 * @property int $languages_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PollQuestionInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PollQuestionInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|PollQuestionInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PollQuestionInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|PollQuestionInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollQuestionInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollQuestionInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollQuestionInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollQuestionInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollQuestionInfo whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollQuestionInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollQuestionInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|PollQuestionInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PollQuestionInfo withoutTrashed()
 */
	class PollQuestionInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PollSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $tab
 * @property int $alerts
 * @property int $user_settings
 * @property int $display_poll
 * @property int $display_survey
 * @property string $tagcloud_shape 1=Cloud,2-Rectangular
 * @property string $tagcloud_colors
 * @property int $projector_refresh_time In seconds
 * @property string $font_size
 * @property int|null $display_poll_module
 * @property int|null $display_survey_module
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereAlerts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereDisplayPoll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereDisplayPollModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereDisplaySurvey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereDisplaySurveyModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereFontSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereProjectorRefreshTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereTagcloudColors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereTagcloudShape($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollSetting whereUserSettings($value)
 */
	class PollSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PollTemplate
 *
 * @property int $id
 * @property int $event_id
 * @property string|null $name
 * @property string $position
 * @property string $preview_image
 * @property int $status
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate wherePreviewImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PollTemplate whereUpdatedAt($value)
 */
	class PollTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PrintLog
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property string $posted_data
 * @property string|null $message
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog wherePostedData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintLog whereUpdatedAt($value)
 */
	class PrintLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PrintPreference
 *
 * @property int $id
 * @property int $event_id
 * @property int $terminal_id
 * @property int $category_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PrintPreference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintPreference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintPreference query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintPreference whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintPreference whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintPreference whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintPreference whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintPreference whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintPreference whereTerminalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintPreference whereUpdatedAt($value)
 */
	class PrintPreference extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PrintSelfCheckIn
 *
 * @property int $id
 * @property int $event_id
 * @property string $active
 * @property string $code
 * @property string $title
 * @property string $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSelfCheckIn whereUpdatedAt($value)
 */
	class PrintSelfCheckIn extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PrintSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int|null $active
 * @property string $username
 * @property string $password
 * @property string $dropdown
 * @property string $sub_category
 * @property int|null $auto_select_subcategory
 * @property int|null $browser
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting whereAutoSelectSubcategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting whereDropdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting whereSubCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintSetting whereUsername($value)
 */
	class PrintSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QA
 *
 * @property int $id
 * @property string $answered
 * @property int $show_projector
 * @property int $rejected
 * @property string $q_startTime
 * @property int $isStart
 * @property int $displayed
 * @property int $sort_order
 * @property int $attendee_id
 * @property int $event_id
 * @property int $agenda_id
 * @property int $speaker_id
 * @property int $anonymous_user
 * @property int $like_count
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|QA newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QA newQuery()
 * @method static \Illuminate\Database\Query\Builder|QA onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|QA query()
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereAnonymousUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereAnswered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereDisplayed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereIsStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereQStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereRejected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereShowProjector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereSpeakerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QA whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|QA withTrashed()
 * @method static \Illuminate\Database\Query\Builder|QA withoutTrashed()
 */
	class QA extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QAAnswer
 *
 * @property int $id
 * @property string $answer
 * @property int $sender_id
 * @property int $qa_id
 * @property int $is_admin
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|QAAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QAAnswer newQuery()
 * @method static \Illuminate\Database\Query\Builder|QAAnswer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|QAAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|QAAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAAnswer whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAAnswer whereQaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAAnswer whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|QAAnswer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|QAAnswer withoutTrashed()
 */
	class QAAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QABccEmail
 *
 * @property int $id
 * @property int $event_id
 * @property int $program_id
 * @property string $bcc_email
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|QABccEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QABccEmail newQuery()
 * @method static \Illuminate\Database\Query\Builder|QABccEmail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|QABccEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder|QABccEmail whereBccEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QABccEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QABccEmail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QABccEmail whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QABccEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QABccEmail whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QABccEmail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|QABccEmail withTrashed()
 * @method static \Illuminate\Database\Query\Builder|QABccEmail withoutTrashed()
 */
	class QABccEmail extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QAInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $qa_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|QAInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo whereQaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QAInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|QAInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|QAInfo withoutTrashed()
 */
	class QAInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QALike
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $qa_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|QALike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QALike newQuery()
 * @method static \Illuminate\Database\Query\Builder|QALike onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|QALike query()
 * @method static \Illuminate\Database\Eloquent\Builder|QALike whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QALike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QALike whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QALike whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QALike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QALike whereQaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QALike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|QALike withTrashed()
 * @method static \Illuminate\Database\Query\Builder|QALike withoutTrashed()
 */
	class QALike extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QASetting
 *
 * @property int $id
 * @property int $countdown_time
 * @property int $parallel_session_projector
 * @property int $project_list_time
 * @property int $max_project_list_time
 * @property int $event_id
 * @property int $qa_answers_view
 * @property int $send_attendee_email
 * @property int $show_attendee_popup
 * @property int $moderator
 * @property int $projector_program
 * @property int $organizer_info
 * @property int $archive
 * @property int $up_vote
 * @property int $qa_listing
 * @property int $anonymous
 * @property int $order_by_likes
 * @property string $background_color
 * @property string $headings_color
 * @property string $description_color
 * @property string $program_section_color
 * @property float $font_size
 * @property int|null $show_profile_images
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|QASetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereArchive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereCountdownTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereDescriptionColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereFontSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereHeadingsColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereMaxProjectListTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereModerator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereOrderByLikes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereOrganizerInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereParallelSessionProjector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereProgramSectionColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereProjectListTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereProjectorProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereQaAnswersView($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereQaListing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereSendAttendeeEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereShowAttendeePopup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereShowProfileImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereUpVote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QASetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|QASetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|QASetting withoutTrashed()
 */
	class QASetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReportingAgentSetting
 *
 * @property int $id
 * @property int $organizer_id
 * @property int $order_number
 * @property int $order_date
 * @property int $name_email
 * @property int $job_title
 * @property int $company
 * @property int $amount
 * @property int $sales_agent
 * @property int $order_status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|ReportingAgentSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereNameEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereOrderStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereSalesAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingAgentSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ReportingAgentSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ReportingAgentSetting withoutTrashed()
 */
	class ReportingAgentSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReportingRevenueTable
 *
 * @property int $id
 * @property int $event_id
 * @property string $order_ids
 * @property string $waiting_order_ids
 * @property string $date
 * @property int $total_tickets
 * @property int $waiting_tickets
 * @property int $event_total_tickets
 * @property float $total_revenue
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable newQuery()
 * @method static \Illuminate\Database\Query\Builder|ReportingRevenueTable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereEventTotalTickets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereOrderIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereTotalRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereTotalTickets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereWaitingOrderIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportingRevenueTable whereWaitingTickets($value)
 * @method static \Illuminate\Database\Query\Builder|ReportingRevenueTable withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ReportingRevenueTable withoutTrashed()
 */
	class ReportingRevenueTable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Reservation
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time_from
 * @property string|null $time_to
 * @property int|null $duration
 * @property int|null $entity_id
 * @property string|null $entity_type
 * @property int|null $organizer_id
 * @property int|null $event_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereTimeFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereTimeTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereUpdatedAt($value)
 */
	class Reservation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReservationLog
 *
 * @property int $id
 * @property int $slot_id
 * @property string $date
 * @property string $time_from
 * @property string $time_to
 * @property int $duration
 * @property int $entity_id
 * @property string $entity_type
 * @property int $organizer_id
 * @property int $event_id
 * @property int $contact_id
 * @property int $reserved_by
 * @property string $reserved_date
 * @property string $status
 * @property string $notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereReservedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereReservedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereSlotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereTimeFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereTimeTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationLog whereUpdatedAt($value)
 */
	class ReservationLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReservationSlot
 *
 * @property int $id
 * @property int $master_id
 * @property string $date
 * @property string $time_from
 * @property string $time_to
 * @property int $duration
 * @property int $entity_id
 * @property string $entity_type
 * @property int $organizer_id
 * @property int $event_id
 * @property int $contact_id
 * @property int $reserved_by
 * @property string $notes
 * @property string $company_name
 * @property string $reserved_date
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereMasterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereReservedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereReservedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereTimeFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereTimeTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlot whereUpdatedAt($value)
 */
	class ReservationSlot extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReservationSlotColleagueAttendee
 *
 * @property int $id
 * @property int $event_id
 * @property int $entity_id
 * @property string $entity_type
 * @property int $slot_id
 * @property int $attendee_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|ReservationSlotColleagueAttendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee whereSlotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationSlotColleagueAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ReservationSlotColleagueAttendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ReservationSlotColleagueAttendee withoutTrashed()
 */
	class ReservationSlotColleagueAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SaleAgent
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $image
 * @property string $password
 * @property string $company
 * @property string $title
 * @property string $status
 * @property int $send_password
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent newQuery()
 * @method static \Illuminate\Database\Query\Builder|SaleAgent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent query()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereSendPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SaleAgent withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SaleAgent withoutTrashed()
 */
	class SaleAgent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SaleAgentEmailTemplate
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $template
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgentEmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgentEmailTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|SaleAgentEmailTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgentEmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgentEmailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgentEmailTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgentEmailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgentEmailTemplate whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgentEmailTemplate whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleAgentEmailTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SaleAgentEmailTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SaleAgentEmailTemplate withoutTrashed()
 */
	class SaleAgentEmailTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SaleType
 *
 * @property int $id
 * @property int $organizer_id
 * @property string $name
 * @property string $code
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType newQuery()
 * @method static \Illuminate\Database\Query\Builder|SaleType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType query()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SaleType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SaleType withoutTrashed()
 */
	class SaleType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SalesforceApiLog
 *
 * @property int $id
 * @property int $organizer_id
 * @property int|null $attendee_id
 * @property string $object
 * @property string|null $alias
 * @property string $action
 * @property string $input
 * @property string $response
 * @property int $status_code
 * @property int $success
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereInput($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereObject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceApiLog whereUpdatedAt($value)
 */
	class SalesforceApiLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SalesforceToken
 *
 * @property int $id
 * @property string $access_token
 * @property string $refresh_token
 * @property string $instance_base_url
 * @property int $user_id
 * @property string|null $expires
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken newQuery()
 * @method static \Illuminate\Database\Query\Builder|SalesforceToken onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken whereInstanceBaseUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesforceToken whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|SalesforceToken withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SalesforceToken withoutTrashed()
 */
	class SalesforceToken extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SessionNew
 *
 * @property int $id
 * @property int|null $attendee_id
 * @property string|null $user_email
 * @property int|null $event_id
 * @property int $site_type 0=Admin,1=Front
 * @property string $ip_address
 * @property string $user_agent
 * @property string $payload
 * @property int $last_activity
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew query()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew whereLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew whereSiteType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionNew whereUserEmail($value)
 */
	class SessionNew extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SocialMedia
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property string $value
 * @property string $select_type
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia newQuery()
 * @method static \Illuminate\Database\Query\Builder|SocialMedia onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereSelectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|SocialMedia withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SocialMedia withoutTrashed()
 */
	class SocialMedia extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SocialMediaFeed
 *
 * @property int $id
 * @property int $event_id
 * @property string $fb_javascript
 * @property string $fb_html
 * @property string $twitter_html
 * @property string $instagram_html
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed newQuery()
 * @method static \Illuminate\Database\Query\Builder|SocialMediaFeed onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed whereFbHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed whereFbJavascript($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed whereInstagramHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed whereTwitterHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeed whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SocialMediaFeed withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SocialMediaFeed withoutTrashed()
 */
	class SocialMediaFeed extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SocialMediaFeedSetting
 *
 * @property int $id
 * @property int $event_id
 * @property string $hash_label
 * @property string $background_color
 * @property string $background_image
 * @property int $refresh_time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|SocialMediaFeedSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting whereBackgroundImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting whereHashLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting whereRefreshTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMediaFeedSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SocialMediaFeedSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SocialMediaFeedSetting withoutTrashed()
 */
	class SocialMediaFeedSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SocialWallPost
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property string $content
 * @property string $image
 * @property string $image_height
 * @property string $image_width
 * @property string $type
 * @property int $likes_count
 * @property int $comments_count
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost newQuery()
 * @method static \Illuminate\Database\Query\Builder|SocialWallPost onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereImageHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereImageWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SocialWallPost withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SocialWallPost withoutTrashed()
 */
	class SocialWallPost extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SocialWallSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $per_page
 * @property string $hash_label
 * @property string $background_color
 * @property string $background_image
 * @property int $organizer_info
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|SocialWallSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting whereBackgroundImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting whereHashLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting whereOrganizerInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting wherePerPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialWallSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SocialWallSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SocialWallSetting withoutTrashed()
 */
	class SocialWallSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SpeakerRequest
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerRequest newQuery()
 * @method static \Illuminate\Database\Query\Builder|SpeakerRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerRequest query()
 * @method static \Illuminate\Database\Query\Builder|SpeakerRequest withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SpeakerRequest withoutTrashed()
 */
	class SpeakerRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SpeakerSetting
 *
 * @property int $id
 * @property int $event_id
 * @property int $phone
 * @property int $email
 * @property int $title
 * @property int $department
 * @property int $company_name
 * @property int $show_country
 * @property int $contact_vcf
 * @property int $program
 * @property int $group
 * @property int $category_group
 * @property int $show_group
 * @property int $show_document
 * @property int $initial
 * @property int $chat
 * @property int $hide_attendee
 * @property int $tab
 * @property string $default_display
 * @property string $order_by
 * @property int $registration_site_limit
 * @property int $poll
 * @property int $document
 * @property int $delegate_number
 * @property int $network_group
 * @property int $table_number
 * @property int $organization
 * @property int $interest
 * @property int $bio_info
 * @property int $show_custom_field
 * @property int $show_industry
 * @property int $show_job_tasks
 * @property int $gdpr_accepted
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereBioInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereCategoryGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereChat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereContactVcf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereDefaultDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereDelegateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereGdprAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereHideAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereInterest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereNetworkGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereOrderBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting wherePoll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereRegistrationSiteLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereShowCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereShowCustomField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereShowDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereShowGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereShowIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereShowJobTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereTableNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpeakerSetting whereUpdatedAt($value)
 */
	class SpeakerSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SponsorAttendee
 *
 * @property int $id
 * @property int $sponsor_id
 * @property int $attendee_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorAttendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|SponsorAttendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorAttendee whereSponsorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SponsorAttendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SponsorAttendee withoutTrashed()
 */
	class SponsorAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SponsorInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $sponsor_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|SponsorInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo whereSponsorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|SponsorInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SponsorInfo withoutTrashed()
 */
	class SponsorInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SponsorNote
 *
 * @property int $id
 * @property int $event_id
 * @property string $notes
 * @property int $attendee_id
 * @property int $sponsor_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorNote newQuery()
 * @method static \Illuminate\Database\Query\Builder|SponsorNote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorNote whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorNote whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorNote whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorNote whereSponsorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SponsorNote withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SponsorNote withoutTrashed()
 */
	class SponsorNote extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SponsorSetting
 *
 * @property int $id
 * @property int $event_id
 * @property string $sponsor_list
 * @property int $sponsorName
 * @property int $sponsorPhone
 * @property int $sponsorEmail
 * @property int $contact_person_email
 * @property int $sponsorContact
 * @property int $sponsorTab
 * @property int $catTab
 * @property int $sortType
 * @property int $hide_attendee
 * @property int $mark_favorite
 * @property int $poll
 * @property int $document
 * @property int $reservation
 * @property int $reservation_type
 * @property int $reservation_req_type_email
 * @property int $reservation_req_type_sms
 * @property int $reservation_allow_contact_person
 * @property int $reservation_allow_multiple
 * @property int $allow_card_reader
 * @property int $show_contact_person
 * @property int $gdpr_accepted
 * @property int $recieve_lead_email_on_save
 * @property int $auto_save
 * @property string|null $bcc_emails
 * @property int $show_lead_email_button
 * @property int $reservation_icone_view
 * @property int $reservations_overview
 * @property int $reservation_overview_icone
 * @property int $reservations_view
 * @property int $reservation_display_filters
 * @property int $reservation_time_slots
 * @property int $reservation_available_meeting_rooms
 * @property int $reservation_meeting_rooms
 * @property int $reservation_display_colleagues
 * @property int $reservation_display_company
 * @property int $colleague_book_meeting
 * @property int $show_sponsor_notes
 * @property int $show_booth
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|SponsorSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereAllowCardReader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereAutoSave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereBccEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereCatTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereColleagueBookMeeting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereContactPersonEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereGdprAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereHideAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereMarkFavorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting wherePoll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereRecieveLeadEmailOnSave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationAllowContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationAllowMultiple($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationAvailableMeetingRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationDisplayColleagues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationDisplayCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationDisplayFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationIconeView($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationMeetingRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationOverviewIcone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationReqTypeEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationReqTypeSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationTimeSlots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationsOverview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereReservationsView($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereShowBooth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereShowContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereShowLeadEmailButton($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereShowSponsorNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereSortType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereSponsorContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereSponsorEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereSponsorList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereSponsorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereSponsorPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereSponsorTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SponsorSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SponsorSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SponsorSetting withoutTrashed()
 */
	class SponsorSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SubAdminEvent
 *
 * @property int $id
 * @property int $admin_id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminEvent newQuery()
 * @method static \Illuminate\Database\Query\Builder|SubAdminEvent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminEvent whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminEvent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminEvent whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SubAdminEvent withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SubAdminEvent withoutTrashed()
 */
	class SubAdminEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SubAdminLicence
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicence newQuery()
 * @method static \Illuminate\Database\Query\Builder|SubAdminLicence onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicence query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicence whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicence whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicence whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicence whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SubAdminLicence withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SubAdminLicence withoutTrashed()
 */
	class SubAdminLicence extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SubAdminLicenceAssign
 *
 * @property int $id
 * @property int $licence_id
 * @property int $organizer_id
 * @property int $status
 * @property string $licence_start_date
 * @property string $licence_end_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubAdminLicenceAssignSubAdmin[] $licenceUsed
 * @property-read int|null $licence_used_count
 * @property-read \App\Models\SubAdminLicence $licences
 * @property-read \App\Models\Organizer $organizer
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign newQuery()
 * @method static \Illuminate\Database\Query\Builder|SubAdminLicenceAssign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign whereLicenceEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign whereLicenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign whereLicenceStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SubAdminLicenceAssign withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SubAdminLicenceAssign withoutTrashed()
 */
	class SubAdminLicenceAssign extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SubAdminLicenceAssignSubAdmin
 *
 * @property int $id
 * @property int $assign_licence_id
 * @property int $sub_admin_id
 * @property int $status
 * @property string $licence_start_date
 * @property string $licence_end_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\SubAdminLicenceAssign $assignLicence
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin newQuery()
 * @method static \Illuminate\Database\Query\Builder|SubAdminLicenceAssignSubAdmin onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin whereAssignLicenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin whereLicenceEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin whereLicenceStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin whereSubAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubAdminLicenceAssignSubAdmin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SubAdminLicenceAssignSubAdmin withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SubAdminLicenceAssignSubAdmin withoutTrashed()
 */
	class SubAdminLicenceAssignSubAdmin extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SurveyAttendee
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $company_name
 * @property int $attendee_id
 * @property int $survey_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|SurveyAttendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SurveyAttendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SurveyAttendee withoutTrashed()
 */
	class SurveyAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SurveyQuestionInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $question_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|SurveyQuestionInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SurveyQuestionInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|SurveyQuestionInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SurveyQuestionInfo withoutTrashed()
 */
	class SurveyQuestionInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TempAttendee
 *
 * @property int $id
 * @property string|null $verification_id
 * @property int $organizer_id
 * @property int $event_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $delegate_number
 * @property string $table_number
 * @property string $password
 * @property string $age
 * @property string $gender
 * @property string $image
 * @property string $company_name
 * @property string $title
 * @property string $industry
 * @property string $about
 * @property string $phone
 * @property string $website
 * @property string $facebook
 * @property string $twitter
 * @property string $linkedin
 * @property string $linkedin_profile_id
 * @property string $fbprofile_id
 * @property string|null $fb_token
 * @property string $fb_url
 * @property string $registration_type
 * @property string $country
 * @property string $organization
 * @property string $jobs
 * @property string $interests
 * @property int $allow_vote 1=Yes, 0=No
 * @property string $initial
 * @property string $department
 * @property int $custom_field_id
 * @property string|null $network_group
 * @property int $billing_ref_attendee
 * @property string $billing_password
 * @property int|null $isActivated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|TempAttendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereAllowVote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereBillingPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereBillingRefAttendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereCustomFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereDelegateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereFbToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereFbUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereFbprofileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereInterests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereIsActivated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereJobs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereLinkedinProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereNetworkGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereRegistrationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereTableNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereVerificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempAttendee whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|TempAttendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TempAttendee withoutTrashed()
 */
	class TempAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TemplateCampaign
 *
 * @property int $id
 * @property int $parent_id
 * @property int $event_id
 * @property int $organizer_id
 * @property string $subject
 * @property string $list_type
 * @property int $template_id
 * @property int|null $l_t_id
 * @property string|null $l_t_type
 * @property string|null $template
 * @property string|null $status
 * @property string|null $schedule_date
 * @property string|null $schedule_time
 * @property string|null $sent_datetime
 * @property string|null $utc_datetime
 * @property string $schedule_repeat
 * @property int|null $repeat_every_qty
 * @property string|null $repeat_every_type
 * @property string|null $repeat_every_on
 * @property string|null $end_type
 * @property string|null $end_on
 * @property int|null $end_after
 * @property int $in_progress
 * @property int $send
 * @property int $deferral
 * @property int $hard_bounce
 * @property int $soft_bounce
 * @property int $open
 * @property int $click
 * @property int $reject
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign newQuery()
 * @method static \Illuminate\Database\Query\Builder|TemplateCampaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign query()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereClick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereDeferral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereEndAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereEndOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereEndType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereHardBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereInProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereLTId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereLTType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereListType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereReject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereRepeatEveryOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereRepeatEveryQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereRepeatEveryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereScheduleDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereScheduleRepeat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereScheduleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereSend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereSentDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereSoftBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaign whereUtcDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|TemplateCampaign withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TemplateCampaign withoutTrashed()
 */
	class TemplateCampaign extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TemplateCampaignLog
 *
 * @property int $id
 * @property int $attendee_id
 * @property string|null $attendee_email
 * @property int $campaign_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaignLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaignLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaignLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaignLog whereAttendeeEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaignLog whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaignLog whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaignLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaignLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateCampaignLog whereUpdatedAt($value)
 */
	class TemplateCampaignLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TemplateMaster
 *
 * @property int $id
 * @property string $alias
 * @property string $type
 * @property string $title
 * @property string $subject
 * @property string $template
 * @property string|null $content
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster query()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMaster whereUpdatedAt($value)
 */
	class TemplateMaster extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Timezone
 *
 * @property int $id
 * @property string $name
 * @property string $timezone
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone query()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereUpdatedAt($value)
 */
	class Timezone extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TrackInfo
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $track_id
 * @property int $languages_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|TrackInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo whereLanguagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo whereTrackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrackInfo whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|TrackInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TrackInfo withoutTrashed()
 */
	class TrackInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\URLShortner
 *
 * @property int $id
 * @property int|null $attendee_id
 * @property int|null $event_id
 * @property int|null $organizer_id
 * @property string $long_url
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|URLShortner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|URLShortner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|URLShortner query()
 * @method static \Illuminate\Database\Eloquent\Builder|URLShortner whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|URLShortner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|URLShortner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|URLShortner whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|URLShortner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|URLShortner whereLongUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|URLShortner whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|URLShortner whereUpdatedAt($value)
 */
	class URLShortner extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WaitingListAttendee
 *
 * @property int $id
 * @property int $event_id
 * @property int $attendee_id
 * @property int $status
 * @property string $order_data
 * @property int $type 1=Waiting List,2=Mister Tango
 * @property string $date_sent
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee newQuery()
 * @method static \Illuminate\Database\Query\Builder|WaitingListAttendee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee query()
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee whereAttendeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee whereDateSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee whereOrderData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingListAttendee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|WaitingListAttendee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|WaitingListAttendee withoutTrashed()
 */
	class WaitingListAttendee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WebServiceRequestLog
 *
 * @property int $id
 * @property string|null $data
 * @property string|null $endpoint
 * @property string|null $date
 * @method static \Illuminate\Database\Eloquent\Builder|WebServiceRequestLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WebServiceRequestLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|WebServiceRequestLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WebServiceRequestLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|WebServiceRequestLog whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebServiceRequestLog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebServiceRequestLog whereEndpoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebServiceRequestLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|WebServiceRequestLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|WebServiceRequestLog withoutTrashed()
 */
	class WebServiceRequestLog extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property int $parent_id
 * @property string $first_name
 * @property string $last_name
 * @property string $user_name
 * @property string $email
 * @property string $password
 * @property string $phone
 * @property string $address
 * @property string $house_number
 * @property string $company
 * @property string $vat_number
 * @property string $zip_code
 * @property string $city
 * @property int $country
 * @property string $create_date
 * @property string $expire_date
 * @property string $domain
 * @property int $total_space
 * @property int $space_private_document
 * @property int|null $sub_admin_limit
 * @property string $status 1= Active, 2 = Pending, 3 = Expire
 * @property string $user_type
 * @property int $internal_organizer
 * @property string $legal_contact_first_name
 * @property string $export_setting
 * @property string $legal_contact_last_name
 * @property string $legal_contact_email
 * @property string $legal_contact_mobile
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @property string $remember_token
 * @property int $show_native_app_link_all_events
 * @property int $allow_native_app
 * @property string $api_key
 * @property int $allow_api
 * @property int $allow_card_reader
 * @property int $white_label_email
 * @property int $authentication
 * @property int $authentication_type
 * @property string $authentication_code
 * @property int $email_marketing_template
 * @property int $mailing_list
 * @property string $authentication_created_date
 * @property string $license_start_date
 * @property string $license_end_date
 * @property string $license_type
 * @property int $paid
 * @property int $eventbuizz_app
 * @property int $white_label_app
 * @property int $allow_admin_access
 * @property int $allow_plug_and_play_access
 * @property string|null $language_id
 * @property string|null $last_login_ip
 * @property int $auto_renewal
 * @property int $notice_period
 * @property string $owner
 * @property string $contact_name
 * @property string $contact_email
 * @property string $notes
 * @property string $terminated_on
 * @property int $allow_nem_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAllowAdminAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAllowApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAllowCardReader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAllowNativeApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAllowNemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAllowPlugAndPlayAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAuthentication($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAuthenticationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAuthenticationCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAuthenticationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAutoRenewal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailMarketingTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEventbuizzApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereExportSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHouseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereInternalOrganizer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLegalContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLegalContactFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLegalContactLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLegalContactMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLicenseEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLicenseStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLicenseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMailingList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNoticePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShowNativeAppLinkAllEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSpacePrivateDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSubAdminLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTerminatedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTotalSpace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWhiteLabelApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWhiteLabelEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereZipCode($value)
 */
	class User extends \Eloquent {}
}


export interface LoginResponse {
  status: number;
  message: string;
  data: LoginResponseData;
}

export interface LoginResponseData {
  event: Event;
  organizer: Organizer;
  checkInPrintSettings: CheckInPrintSettings;
  terminals: Terminal[];
  access_token: string;
  token_type: string;
  expires_at: string;
  user: User;
}

export interface User {
  id: number;
  name: string;
  email: string;
  event_id?: null|number;
}

export interface Terminal {
  value: string;
  name: string;
  id: number | string;
  event_id?: number;
  type?: string;
  sort_order?: number;
  created_at?: string;
  updated_at?: string;
  deleted_at?: null|string;
}

export interface CheckInPrintSettings {
  fairkey_active: string;
  auto_select_subcategory: string;
}

export interface Organizer {
  id: number;
  parent_id: number;
  first_name: string;
  last_name: string;
  user_name: string;
  email: string;
  phone: string;
  address: string;
  house_number: string;
  company: string;
  vat_number: string;
  zip_code: string;
  city: string;
  country: number;
  create_date: string;
  expire_date: string;
  domain: string;
  total_space: number;
  space_private_document: number;
  sub_admin_limit?: any;
  plugnplay_sub_admin_limit: number;
  status: string;
  user_type: string;
  internal_organizer: number;
  legal_contact_first_name?: any;
  export_setting: string;
  legal_contact_last_name?: any;
  legal_contact_email?: any;
  legal_contact_mobile?: any;
  created_at: string;
  updated_at: string;
  deleted_at?: any;
  show_native_app_link_all_events: number;
  allow_native_app: number;
  api_key: string;
  allow_api: number;
  allow_card_reader: number;
  white_label_email: number;
  authentication: number;
  authentication_type: number;
  authentication_code: string;
  email_marketing_template: number;
  mailing_list: number;
  access_plug_play: number;
  authentication_created_date: string;
  license_start_date: string;
  license_end_date: string;
  license_type: string;
  paid: number;
  allow_admin_access: number;
  allow_plug_and_play_access: number;
  allow_nem_id: number;
  eventbuizz_app: number;
  white_label_app: number;
  language_id: string;
  auto_renewal: number;
  notice_period: number;
  owner: string;
  contact_name: string;
  contact_email: string;
  notes: string;
  terminated_on: string;
  last_login_ip?: any;
  token: string;
  token_expire_at: string;
  show_all_events: number;
  crm_integrated: number;
  is_ean: number;
  show_all_events_event_center: number;
  membership_list: number;
}

export interface Event {
  id: number;
  organizer_name: string;
  organizer_id: number;
  name: string;
  url: string;
  start_date: string;
  end_date: string;
  start_time: string;
  end_time: string;
  status: number;
  timezone_id: number;
  language_id: number;
  country_id: number;
}

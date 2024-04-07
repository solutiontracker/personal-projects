
export interface QasessionProgram {
    id? : number;
    event_id?:	number;
    start_date? : string;	
    start_time? : string;	
    link_type? : string;	
    created_at? : string;	
    updated_at? : string;	
    deleted_at? : string;
    workshop_id?: number;
    qa?: number;
    ticket?: number;
    enable_checkin?: number;
    enable_speakerlist?: number;
    hide_on_registrationsite?: number;
    hide_on_app?: number;
    only_for_qa?: number;
    only_for_speaker_list?: number;
    vonageSessionId?: number;	
    show_program_on_check_in_app?: number;
    validate_session_checkin?: number; 
    hide_time?: number;
    activate_json_feed?: number;
    only_for_poll?: number;
    total_question_count?: number;
    replied_question_count?: number;
    new_question_count?: number;
}
export interface QaModule {
    qa_session_programs?: Array<QasessionProgram>[],
}

export interface ModuleResponse {
    data: {
        modules: Array<QaModule>[]
    },
    success?: boolean
}

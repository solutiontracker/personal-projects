export interface QuestionModule {
    id?: number,
    name: string,
    alias: string,
    alert: number,
    section_type?: string,
    active_questions?: Array<any>,
    questions?: Array<any>,
    event?: number,
}

export interface QuestionModuleResponse {
    data: {
        modules: Array<QuestionModule>
    },
    success?: boolean
}
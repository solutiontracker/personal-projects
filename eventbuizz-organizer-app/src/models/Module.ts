export interface Module {
    id?: number,
    name: string,
    alias: string,
    alert: number,
    section_type?: string,
}

export interface ModuleResponse {
    data: {
        modules: Array<Module>
    },
    success?: boolean
}


// Require and initialize outside of your main handler
const connection = require('serverless-mysql')({
    config: {
        host: process.env.AWS_DATABASE_HOST,
        database: process.env.AWS_DATABASE_NAME,
        user: process.env.AWS_DATABASE_USER,
        password: process.env.AWS_DATABASE_PASSWORD,
    }
})

exports.handler = (event, context, callback) => {
    
    const response = {
        statusCode: 200,
        body: JSON.stringify({
            message: 'SQS event processed.',
            input: event,
        }),
    };

    if (event.Records !== undefined && event.Records.length > 0) {
        event.Records.forEach(async record => {
            const jsonData = JSON.parse(record.body);
            var sql = 'INSERT INTO conf_qa (event_id, agenda_id, speaker_id, attendee_id, anonymous_user,show_projector ,answered, created_at, updated_at) VALUES ';
            var sub_sql = '("' + jsonData.event_id + '", "' + jsonData.agenda_id + '", "' + jsonData.speaker_id + '", "' + jsonData.attendee_id + '", "' + jsonData.anonymous_user + '", "' + jsonData.show_projector + '", "' + jsonData.answered + '",  "' + jsonData.created_at + '", "' + jsonData.updated_at + '")';
            if (sub_sql != '') {
                sql += sub_sql;
                connection.query(sql, function (err, result) {
                    if (err) console.error(err.stack);
                    qa_last_insertedID = result.insertId;
                    console.log(result)
                    // QA settings
                    var sql = "SELECT * FROM conf_qa_settings WHERE (event_id='" + jsonData.event_id + "' AND deleted_at IS NULL)";
                    connection.query(sql, function (err, result, fields) {
                        if (err) console.error(err.stack);
                        var qa_modrator_settings = result[0].moderator;
                        /***  Code for sorting ****/
                        var sql = "SELECT * FROM conf_qa WHERE (agenda_id='" + jsonData.agenda_id + "' AND deleted_at IS NULL) ORDER BY sort_order DESC";
                        connection.query(sql, function (err, result, fields) {
                            if (err) console.error(err.stack);
                            if (typeof result !== 'undefined' && result.length > 0) {
                                var $sort_order = result[0].sort_order + 1;
                                var sql = "UPDATE conf_qa SET sort_order = '" + $sort_order + "' WHERE id= '" + qa_last_insertedID + "'";
                                connection.query(sql, function (err, result) {
                                    if (err) console.error(err.stack);
                                });
                            }
                        });
                        /***  END Code for sorting ****/
                        var allLanguages_obj = JSON.parse(jsonData.allLanguages);
                        if (allLanguages_obj.length > 0) {
                            for (var i = 0; i < allLanguages_obj.length; i++) {
                                if (jsonData.question != '') {
                                    var sql = "INSERT INTO conf_qa_info (status,qa_id,languages_id,name, value) VALUES ('1','" + qa_last_insertedID + "','" + allLanguages_obj[i] + "','question', '" + SqlSanitize.escape(jsonData.question).slice(1, -1) + "')";
                                    connection.query(sql, function (err, result) {
                                        if (err) console.error(err.stack);
                                    });
                                }
                                if ($qa_date != '') {
                                    var sql = "INSERT INTO conf_qa_info (status,qa_id,languages_id,name, value) VALUES ('1','" + qa_last_insertedID + "','" + allLanguages_obj[i] + "','question_time', '" + $qa_date + "')";
                                    connection.query(sql, function (err, result) {
                                        if (err) console.error(err.stack);
                                    });
                                }
                                if ($ans == '') {
                                    var sql = "INSERT INTO conf_qa_info (status,qa_id,languages_id,name, value) VALUES ('1','" + qa_last_insertedID + "','" + allLanguages_obj[i] + "','answer', '" + $ans + "')";
                                    connection.query(sql, function (err, result) {
                                        if (err) console.error(err.stack);
                                    });
                                }
                                if ($ans_date != '') {
                                    var sql = "INSERT INTO conf_qa_info (status,qa_id,languages_id,name, value) VALUES ('1','" + qa_last_insertedID + "','" + allLanguages_obj[i] + "','answer_time', '" + $ans_date + "')";
                                    connection.query(sql, function (err, result) {
                                        if (err) console.error(err.stack);
                                    });
                                }
                                if (typeof (jsonData.paragraph_number) !== 'undefined') {
                                    var sql = "INSERT INTO conf_qa_info (status,qa_id,languages_id,name, value) VALUES ('1','" + qa_last_insertedID + "','" + allLanguages_obj[i] + "','paragraph_number', '" + SqlSanitize.escape(jsonData.paragraph_number).slice(1, -1) + "')";
                                    connection.query(sql, function (err, result) {
                                        if (err) console.error(err.stack);
                                    });
                                }
                                if (typeof (jsonData.paragraph_id) !== 'undefined') {
                                    var sql = "INSERT INTO conf_qa_info (status,qa_id,languages_id,name, value) VALUES ('1','" + qa_last_insertedID + "','" + allLanguages_obj[i] + "','paragraph_id', '" + SqlSanitize.escape(jsonData.paragraph_id).slice(1, -1) + "')";
                                    connection.query(sql, function (err, result) {
                                        if (err) console.error(err.stack);
                                    });
                                }
                                if (typeof (jsonData.line_number) !== 'undefined') {
                                    var sql = "INSERT INTO conf_qa_info (status,qa_id,languages_id,name, value) VALUES ('1','" + qa_last_insertedID + "','" + allLanguages_obj[i] + "','line_number', '" + SqlSanitize.escape(jsonData.line_number).slice(1, -1) + "')";
                                    connection.query(sql, function (err, result) {
                                        if (err) console.error(err.stack);
                                    });
                                }

                            }
                        }
                        if (qa_modrator_settings == '0') {
                            var sql = "UPDATE conf_qa SET show_projector = '1' WHERE id= '" + qa_last_insertedID + "'";
                            connection.query(sql, function (err, result) {
                                if (err) console.error(err.stack);
                            });
                            AttendeeWithQuestionData_socket_data_render(qa_last_insertedID, '0', true, result[0]);
                            AttendeeWithQuestionData_socket_modrator_live_render(qa_last_insertedID, '0', true);
                            AttendeeWithQuestionData_socket_recent_popler_render(qa_last_insertedID, '0', true);
                        } else {
                            var sql = "UPDATE conf_qa set show_projector='0', displayed='0', rejected='0', isStart='0' WHERE id= '" + qa_last_insertedID + "'";
                            connection.query(sql, function (err, result) {
                                if (err) console.error(err.stack);
                            });
                            AttendeeWithQuestionData_socket_modrator_incoming_render(qa_last_insertedID, '0', false);
                        }
                    });
                    const options_val = jsonData.base_url + '/socketEmailToOrganizer/' + jsonData.event_id + '/' + jsonData.agenda_id + '/' + jsonData.attendee_id + '/' + qa_last_insertedID;
                    https.get(options_val, (resp) => { }).on("error", (err) => {
                        console.log("Error: " + err.message);
                    });
                });
            }
        });
    }
    callback(null, response);
};

function AttendeeWithQuestionData_socket_data_render(qa_id, is_start, show_projector, qa_setting) {
    var $qa_sql = 'select * from `conf_qa` where `conf_qa`.`deleted_at` is null and `id` = "' + qa_id + '" and `isStart` = "' + is_start + '" ';
    if (show_projector) {
        $qa_sql += 'AND show_projector = "1" AND displayed = "0" ';
    }
    $qa_sql += 'order by `sort_order` asc, `id` desc';
    connection.query($qa_sql, function (err, result1) {
        if (err) console.error(err.stack);
        if (result1.length > 0) {
            var $qa_sql_data = result1;
            var $qa_info_sql = 'select * from `conf_qa_info` where `conf_qa_info`.`deleted_at` is null and `qa_id` = "' + qa_id + '" AND languages_id = "' + jsonData.language_id + '" ';
            connection.query($qa_info_sql, function (err, result2, fields) {
                if (err) throw err;
                var $qa_info_sql_data = result2;
                $qa_info_sql_data_transform = {};
                if ($qa_info_sql_data.length > 0) {
                    for (var i = 0; i < $qa_info_sql_data.length; i++) {
                        $qa_info_sql_data_transform[$qa_info_sql_data[i].name] = $qa_info_sql_data[i].value;
                    }
                }
                var $attendee_info_sql = 'SELECT * FROM `conf_attendees` LEFT JOIN conf_attendees_info ON conf_attendees.id = conf_attendees_info.attendee_id where conf_attendees.`id` = "' + $qa_sql_data[0].attendee_id + '" AND languages_id = "' + jsonData.language_id + '"';
                connection.query($attendee_info_sql, function (err, result3, fields) {
                    if (err) throw err;
                    var $attendee_info_sql_data = result3;
                    $attendee_info_sql_data_transform = {};
                    if ($attendee_info_sql_data.length > 0) {
                        for (var i = 0; i < $attendee_info_sql_data.length; i++) {
                            $attendee_info_sql_data_transform[$attendee_info_sql_data[i].name] = $attendee_info_sql_data[i].value;
                            $attendee_info_sql_data_transform['first_name'] = $attendee_info_sql_data[i].first_name;
                            $attendee_info_sql_data_transform['last_name'] = $attendee_info_sql_data[i].last_name;
                        }
                    }
                    var $speaker_info_sql = 'SELECT * FROM `conf_attendees` LEFT JOIN conf_attendees_info ON conf_attendees.id = conf_attendees_info.attendee_id where conf_attendees.`id` = "' + $qa_sql_data[0].speaker_id + '" AND languages_id = "' + jsonData.language_id + '" ';
                    connection.query($speaker_info_sql, function (err, result4, fields) {
                        if (err) throw err;
                        var $speaker_info_sql_data = result4;
                        $speaker_info_sql_data_transform = {};
                        if ($speaker_info_sql_data.length > 0) {
                            for (var i = 0; i < $speaker_info_sql_data.length; i++) {
                                $speaker_info_sql_data_transform[$speaker_info_sql_data[i].name] = $speaker_info_sql_data[i].value;
                                $speaker_info_sql_data_transform['first_name'] = $speaker_info_sql_data[i].first_name;
                                $speaker_info_sql_data_transform['last_name'] = $speaker_info_sql_data[i].last_name;
                            }
                        }
                        var $qa_likes_sql = 'SELECT * FROM `conf_qa_likes` WHERE `qa_id` = "' + $qa_sql_data[0].id + '" ';
                        connection.query($qa_likes_sql, async function (err, result5, fields) {
                            if (err) throw err;
                            var $qa_likes_sql_data = result5;
                            // Buisness logic START here

                            if (jsonData.enable_gdpr == '0' || (jsonData.enable_gdpr == '1' && jsonData.enable_attendee_gdpr == '1') || (jsonData.enable_gdpr == '1' && jsonData.enable_attendee_gdpr == '0' && jsonData.attendee_invisible)) {
                                var result = await socket_data_render_HTML($qa_sql_data[0], $qa_info_sql_data_transform, $attendee_info_sql_data_transform, $speaker_info_sql_data_transform, $qa_likes_sql_data, qa_setting, 1, $attendee_info_sql_data);
                            }

                            QAAttendeeActionFront_socket(true, false, false, result, false, jsonData.agenda_id);
                            // Buisness logic End here
                        });
                    });
                });
            });
        }
    });
}

async function socket_data_render_HTML($qa_sql_data, $qa_info_sql_data_transform, $attendee_info_sql_data_transform, $speaker_info_sql_data_transform, $qa_likes_sql_data, qa_setting, $status, $attendee_info_sql_data) {
    html = '<li id="' + $qa_sql_data.id + '"> <div class="user-info">';
    if (qa_setting.show_profile_images == "1") {
        html += '<div class="media">';
        if (jsonData.enable_gdpr == '0' || (jsonData.enable_gdpr == '1' && jsonData.enable_attendee_gdpr == '1')) {
            if ($qa_sql_data.anonymous_user == '0') {
                if ($attendee_info_sql_data_transform.image) {
                    html += '<img src="' + jsonData.base_url + 'assets/attendees/' + $attendee_info_sql_data_transform.image + '" width="60" />';
                } else {
                    html += '<img src="' + jsonData.base_url + 'images/speakers/no-img.jpg" width="60" />';
                }
            } else {
                html += '<img src="' + jsonData.base_url + 'images/speakers/no-img.jpg" width="60" />';
            }
        } else {
            html += '<img src="' + jsonData.base_url + 'images/speakers/no-img.jpg" width="60" />';
        }

        html += '</div>';
    }
    html += '<div class="user-title">';
    if ($qa_sql_data.anonymous_user == '0') {
        html += '<strong class="name"> ' + $attendee_info_sql_data_transform.first_name + ' ' + $attendee_info_sql_data_transform.last_name + '</strong>';
        if (jsonData.enable_gdpr == '0' || (jsonData.enable_gdpr == '1' && jsonData.enable_attendee_gdpr == '1')) {
            var info = await getInfo(connection, jsonData.event_id, $attendee_info_sql_data);
            html += info;
        }
    } else {
        html += '<strong class="name">Anonymous</strong>';
    }
    html += '</div>';
    html += '<div class="like"> <span> <img src="' + jsonData.base_url + '_admin_assets/images/thumb-black.svg" /> </span>';
    html += '<span class="number">' + $qa_likes_sql_data.length + '</span>';
    html += '</div> </div> <div class="user-detail">';
    html += '<p>' + $qa_info_sql_data_transform.question + '</p>';
    if (!isEmpty($speaker_info_sql_data_transform)) {
        html += '<ul class="bottom-sec">';
        html += '<li><span class="title">' + jsonData.question_for_label + '</span></li>';
        html += '<li><strong class="name">' + $speaker_info_sql_data_transform.first_name + ' ' + $speaker_info_sql_data_transform.last_name + '</strong>';
        if ($speaker_info_sql_data_transform.title) {
            html += $speaker_info_sql_data_transform.title;
        }
        if ($speaker_info_sql_data_transform.company_name) {
            html += ' ' + $speaker_info_sql_data_transform.company_name;
        }
        html += '</li> </ul>';
    } else if ($qa_sql_data.speaker_id == 1) {
        html += '<ul class="bottom-sec"> <li><span class="title">' + jsonData.question_for_label + '</span></li> <li><strong class="name">All speakers</strong> </li> </ul>';
    }
    html += '</div> </li>';
    return html;
}

async function getInfo(connection, event_id, $attendee_info_sql_data) {
    console.log('calling');
    var x = await resolveAfter2Seconds(connection, event_id, $attendee_info_sql_data);
    return x;
};

function resolveAfter2Seconds(connection, event_id, $attendee_info_sql_data) {
    var html_info = '';
    return new Promise(resolve => {
        var $projector_attendee_fields_sql = 'SELECT * FROM `conf_event_question_projector_attendee_fields` where `event_id` = "' + event_id + '" AND deleted_at is null ORDER BY sort_order ASC ';
        connection.query($projector_attendee_fields_sql, function (err, result3, fields) {
            if (err) throw err;
            var $projector_attendee_fields = result3;
            if ($projector_attendee_fields.length > 0) {
                for (var i = 0; i < $projector_attendee_fields.length; i++) {
                    var info_name = $projector_attendee_fields[i].fields_name;
                    if (info_name == 'email') {
                        info_name = $attendee_info_sql_data[0].email;
                    } else if (info_name == 'EMPLOYMENT_DATE') {
                        if ($attendee_info_sql_data[0].EMPLOYMENT_DATE == '0000-00-00') {
                            info_name = '0000-00-00';
                        } else {
                            info_name = new Date($attendee_info_sql_data[0].EMPLOYMENT_DATE).toISOString().slice(0, 10);
                        }
                    } else if (info_name == 'BIRTHDAY_YEAR') {
                        if ($attendee_info_sql_data[0].BIRTHDAY_YEAR == '0000-00-00 00:00:00') {
                            info_name = '0000-00-00 00:00:00';
                        } else {
                            info_name = new Date($attendee_info_sql_data[0].BIRTHDAY_YEAR).toISOString().slice(0, 10);
                        }
                    } else if (info_name == 'FIRST_NAME_PASSPORT') {
                        info_name = $attendee_info_sql_data[0].FIRST_NAME_PASSPORT;
                    } else if (info_name == 'LAST_NAME_PASSPORT') {
                        info_name = $attendee_info_sql_data[0].LAST_NAME_PASSPORT;
                    } else {
                        info_name = $attendee_info_sql_data_transform[info_name];
                    }
                    html_info += '<span class="designation">';
                    html_info += info_name;
                    html_info += '</span>';
                }
                resolve(html_info);
            } else {
                resolve(html_info);
            }
        });
    });
}

function QAAttendeeActionFront_socket($archive_to_live, $make_live, $is_archive, $data, $is_sort, $agenda_id, $updated_like_count, $update_like_qa_id) {
    $data2 = {
        'event': 'qa_block_' + jsonData.event_id + '_' + jsonData.agenda_id,
        'data': {
            'archive_to_live': $archive_to_live,
            'make_live': $make_live,
            'is_archive': $is_archive,
            'data_info': $data,
            'is_sort': $is_sort,
            'updated_like_count': $updated_like_count,
            'update_like_qa_id': $update_like_qa_id
        }
    };
    $json_string = JSON.stringify($data2);
    redis.publish('event-buizz', $json_string);
}

function AttendeeWithQuestionData_socket_modrator_live_render(qa_id, is_start, show_projector) {
    var $qa_sql = 'select * from `conf_qa` where `conf_qa`.`deleted_at` is null and `id` = "' + qa_id + '" and `isStart` = "' + is_start + '" ';
    if (show_projector) {
        $qa_sql += 'AND show_projector = "1" AND displayed = "0" ';
    }
    $qa_sql += 'order by `sort_order` asc, `id` desc';
    connection.query($qa_sql, function (err, result1) {
        if (err) console.error(err.stack);
        if (result1.length > 0) {
            var $qa_sql_data = result1;
            var $qa_info_sql = 'select * from `conf_qa_info` where `conf_qa_info`.`deleted_at` is null and `qa_id` = "' + qa_id + '" AND languages_id = "' + jsonData.language_id + '" ';
            connection.query($qa_info_sql, function (err, result2, fields) {
                if (err) throw err;
                var $qa_info_sql_data = result2;
                $qa_info_sql_data_transform = {};
                if ($qa_info_sql_data.length > 0) {
                    for (var i = 0; i < $qa_info_sql_data.length; i++) {
                        $qa_info_sql_data_transform[$qa_info_sql_data[i].name] = $qa_info_sql_data[i].value;
                    }
                }
                var $attendee_info_sql = 'SELECT * FROM `conf_attendees` LEFT JOIN conf_attendees_info ON conf_attendees.id = conf_attendees_info.attendee_id where conf_attendees.`id` = "' + $qa_sql_data[0].attendee_id + '" AND languages_id = "' + jsonData.language_id + '"';
                connection.query($attendee_info_sql, function (err, result3, fields) {
                    if (err) throw err;
                    var $attendee_info_sql_data = result3;
                    $attendee_info_sql_data_transform = {};
                    if ($attendee_info_sql_data.length > 0) {
                        for (var i = 0; i < $attendee_info_sql_data.length; i++) {
                            $attendee_info_sql_data_transform[$attendee_info_sql_data[i].name] = $attendee_info_sql_data[i].value;
                            $attendee_info_sql_data_transform['first_name'] = $attendee_info_sql_data[i].first_name;
                            $attendee_info_sql_data_transform['last_name'] = $attendee_info_sql_data[i].last_name;
                        }
                    }
                    var $speaker_info_sql = 'SELECT * FROM `conf_attendees` LEFT JOIN conf_attendees_info ON conf_attendees.id = conf_attendees_info.attendee_id where conf_attendees.`id` = "' + $qa_sql_data[0].speaker_id + '" AND languages_id = "' + jsonData.language_id + '" ';
                    connection.query($speaker_info_sql, function (err, result4, fields) {
                        if (err) throw err;
                        var $speaker_info_sql_data = result4;
                        $speaker_info_sql_data_transform = {};
                        if ($speaker_info_sql_data.length > 0) {
                            for (var i = 0; i < $speaker_info_sql_data.length; i++) {
                                $speaker_info_sql_data_transform[$speaker_info_sql_data[i].name] = $speaker_info_sql_data[i].value;
                                $speaker_info_sql_data_transform['first_name'] = $speaker_info_sql_data[i].first_name;
                                $speaker_info_sql_data_transform['last_name'] = $speaker_info_sql_data[i].last_name;
                            }
                        }
                        var $qa_likes_sql = 'SELECT * FROM `conf_qa_likes` WHERE `qa_id` = "' + $qa_sql_data[0].id + '" ';
                        connection.query($qa_likes_sql, function (err, result5, fields) {
                            if (err) throw err;
                            var $qa_likes_sql_data = result5;
                            // Buisness logic START here
                            var result = socket_modrator_live_render_HTML($qa_sql_data[0], $qa_info_sql_data_transform, $attendee_info_sql_data_transform, $speaker_info_sql_data_transform, $qa_likes_sql_data);
                            QAModratorLiveAction_socket(result, jsonData.agenda_id);
                            // Buisness logic End here
                        });
                    });
                });
            });
        }
    });
}

function socket_modrator_live_render_HTML($qa_sql_data, $qa_info_sql_data_transform, $attendee_info_sql_data_transform, $speaker_info_sql_data_transform, $qa_likes_sql_data) {
    var html = '<section class="item" id="live_data_' + $qa_sql_data.id + '"><div class="text" id="' + $qa_sql_data.id + '"><h2>';
    if ($qa_sql_data.anonymous_user == 1) {
        html += 'Anonymous';
    } else {
        html += $attendee_info_sql_data_transform.first_name + ' ' + $attendee_info_sql_data_transform.last_name;
    }
    html += '</h2>';
    if ($qa_info_sql_data_transform.paragraph_number) {
        html += '<p class="paragraph_number">' + jsonData.QA_MODERATOR_PARAGRAPH + ': ' + $qa_info_sql_data_transform.paragraph_number + '</p>';
    }
    if ($qa_info_sql_data_transform.line_number) {
        html += '<p>' + jsonData.QA_MODERATOR_LINE_NUMBER + ': ' + $qa_info_sql_data_transform.line_number + '</p>';
    }
    html += '<time datetime=" ' + dateFormat(jsonData.created_at, "d  mmm. yy HH:MM") + '" > ' + dateFormat(jsonData.created_at, "d  mmm. yy HH:MM") + ' </time>';
    if ($qa_info_sql_data_transform.question) {
        html += '<p>' + $qa_info_sql_data_transform.question + '</p>';
    }
    html += '<div class="speaker_info"><b>Speaker:</b>';
    if ($qa_sql_data.speaker_id == 0) {
        html += 'N/A';
    } else {
        if ($qa_sql_data.speaker_id == 1) {
            html += 'All Speakers';
        } else {
            html += $speaker_info_sql_data_transform.first_name + ' ' + $speaker_info_sql_data_transform.last_name;
        }
    }
    html += '</div>';
    html += '</div>';
    html += '<ul class="inline-list"> <li><a href="javascript:void(0);" onClick="makeLive(' + $qa_sql_data.id + ');"> <img src="' + jsonData.base_url + '_admin_assets/images/archived-1.png" /> </a></li> <li><a href="javascript:void(0);" onClick="liveArchive(' + $qa_sql_data.id + ');"> <img src="' + jsonData.base_url + '_admin_assets/images/archived-2.png" /></a></li> </ul> </section>';
    return html;
}

function QAModratorLiveAction_socket($data, $agenda_id) {
    $data2 = {
        'event': 'qa_block_modrator_live_' + jsonData.event_id + '_' + jsonData.agenda_id,
        'data': {
            'data_info': $data
        }
    };
    $json_string = JSON.stringify($data2);
    redis.publish('event-buizz', $json_string);
}

function AttendeeWithQuestionData_socket_modrator_incoming_render(qa_id, is_start, show_projector) {
    var $qa_sql = 'select * from `conf_qa` where `conf_qa`.`deleted_at` is null and `id` = "' + qa_id + '" and `isStart` = "' + is_start + '" ';
    if (show_projector) {
        $qa_sql += 'AND show_projector = "1" AND displayed = "0" ';
    }
    $qa_sql += 'order by `sort_order` asc, `id` desc';
    connection.query($qa_sql, function (err, result1) {
        if (err) console.error(err.stack);
        if (result1.length > 0) {
            var $qa_sql_data = result1;
            var $qa_info_sql = 'select * from `conf_qa_info` where `conf_qa_info`.`deleted_at` is null and `qa_id` = "' + qa_id + '" AND languages_id = "' + jsonData.language_id + '" ';
            connection.query($qa_info_sql, function (err, result2, fields) {
                if (err) throw err;
                var $qa_info_sql_data = result2;
                $qa_info_sql_data_transform = {};
                if ($qa_info_sql_data.length > 0) {
                    for (var i = 0; i < $qa_info_sql_data.length; i++) {
                        $qa_info_sql_data_transform[$qa_info_sql_data[i].name] = $qa_info_sql_data[i].value;
                    }
                }
                var $attendee_info_sql = 'SELECT * FROM `conf_attendees` LEFT JOIN conf_attendees_info ON conf_attendees.id = conf_attendees_info.attendee_id where conf_attendees.`id` = "' + $qa_sql_data[0].attendee_id + '" AND languages_id = "' + jsonData.language_id + '"';
                connection.query($attendee_info_sql, function (err, result3, fields) {
                    if (err) throw err;
                    var $attendee_info_sql_data = result3;
                    $attendee_info_sql_data_transform = {};
                    if ($attendee_info_sql_data.length > 0) {
                        for (var i = 0; i < $attendee_info_sql_data.length; i++) {
                            $attendee_info_sql_data_transform[$attendee_info_sql_data[i].name] = $attendee_info_sql_data[i].value;
                            $attendee_info_sql_data_transform['first_name'] = $attendee_info_sql_data[i].first_name;
                            $attendee_info_sql_data_transform['last_name'] = $attendee_info_sql_data[i].last_name;
                        }
                    }
                    var $speaker_info_sql = 'SELECT * FROM `conf_attendees` LEFT JOIN conf_attendees_info ON conf_attendees.id = conf_attendees_info.attendee_id where conf_attendees.`id` = "' + $qa_sql_data[0].speaker_id + '" AND languages_id = "' + jsonData.language_id + '" ';
                    connection.query($speaker_info_sql, function (err, result4, fields) {
                        if (err) throw err;
                        var $speaker_info_sql_data = result4;
                        $speaker_info_sql_data_transform = {};
                        if ($speaker_info_sql_data.length > 0) {
                            for (var i = 0; i < $speaker_info_sql_data.length; i++) {
                                $speaker_info_sql_data_transform[$speaker_info_sql_data[i].name] = $speaker_info_sql_data[i].value;
                                $speaker_info_sql_data_transform['first_name'] = $speaker_info_sql_data[i].first_name;
                                $speaker_info_sql_data_transform['last_name'] = $speaker_info_sql_data[i].last_name;
                            }
                        }
                        var $qa_likes_sql = 'SELECT * FROM `conf_qa_likes` WHERE `qa_id` = "' + $qa_sql_data[0].id + '" ';
                        connection.query($qa_likes_sql, function (err, result5, fields) {
                            if (err) throw err;
                            var $qa_likes_sql_data = result5;
                            // Buisness logic START here
                            var result = socket_modrator_incoming_render_HTML($qa_sql_data[0], $qa_info_sql_data_transform, $attendee_info_sql_data_transform, $speaker_info_sql_data_transform, $qa_likes_sql_data);
                            QAModratorIncomingAction_socket(result, jsonData.agenda_id);
                            // Buisness logic End here
                        });
                    });
                });
            });
        }
    });
}

function socket_modrator_incoming_render_HTML($qa_sql_data, $qa_info_sql_data_transform, $attendee_info_sql_data_transform, $speaker_info_sql_data_transform, $qa_likes_sql_data) {
    var html = '<section class="item" id="incoming_data_' + $qa_sql_data.id + '"><div class="text" id="' + $qa_sql_data.id + '"><h2>';
    if ($qa_sql_data.anonymous_user == 1) {
        html += 'Anonymous';
    } else {
        html += $attendee_info_sql_data_transform.first_name + ' ' + $attendee_info_sql_data_transform.last_name;
    }
    html += '</h2>';
    if ($qa_info_sql_data_transform.paragraph_number) {
        html += '<p class="paragraph_number">' + jsonData.QA_MODERATOR_PARAGRAPH + ': ' + $qa_info_sql_data_transform.paragraph_number + '</p>';
    }
    if ($qa_info_sql_data_transform.line_number) {
        html += '<p>' + jsonData.QA_MODERATOR_LINE_NUMBER + ': ' + $qa_info_sql_data_transform.line_number + '</p>';
    }
    html += '<time datetime=" ' + dateFormat(jsonData.created_at, "d  mmm. yy HH:MM") + '" > ' + dateFormat(jsonData.created_at, "d  mmm. yy HH:MM") + ' </time>';
    if ($qa_info_sql_data_transform.question) {
        html += '<p>' + $qa_info_sql_data_transform.question + '</p>';
    }
    html += '<div class="speaker_info"><b>Speaker:</b>';
    if ($qa_sql_data.speaker_id == 0) {
        html += 'N/A';
    } else {
        if ($qa_sql_data.speaker_id == 1) {
            html += 'All Speakers';
        } else {
            html += $speaker_info_sql_data_transform.first_name + ' ' + $speaker_info_sql_data_transform.last_name;
        }
    }
    html += '</div>';
    html += '</div>';
    html += '<ul class="inline-list"> <li><a href="javascript:void(0);" onClick="incomingLive(' + $qa_sql_data.id + ');"> <img src="' + jsonData.base_url + '_admin_assets/images/yes-1.png" /> </a></li> <li><a href="javascript:void(0);" onClick="incomingReject(' + $qa_sql_data.id + ');"> <img src="' + jsonData.base_url + '_admin_assets/images/close-1.png" /></a></li> </ul> </section>';
    return html;
}

function QAModratorIncomingAction_socket($data, $agenda_id) {
    $data2 = {
        'event': 'qa_block_modrator_incoming_' + jsonData.event_id + '_' + jsonData.agenda_id,
        'data': {
            'data_info': $data
        }
    };
    $json_string = JSON.stringify($data2);
    redis.publish('event-buizz', $json_string);
}

function AttendeeWithQuestionData_socket_recent_popler_render(qa_id, is_start, show_projector) {
    var $qa_sql = 'select * from `conf_qa` where `conf_qa`.`deleted_at` is null and `id` = "' + qa_id + '" and `isStart` = "' + is_start + '" ';
    if (show_projector) {
        $qa_sql += 'AND show_projector = "1" AND displayed = "0" ';
    }
    $qa_sql += 'order by `sort_order` asc, `id` desc';
    connection.query($qa_sql, function (err, result1) {
        if (err) console.error(err.stack);
        if (result1.length > 0) {
            var $qa_sql_data = result1;
            var $qa_info_sql = 'select * from `conf_qa_info` where `conf_qa_info`.`deleted_at` is null and `qa_id` = "' + qa_id + '" AND languages_id = "' + jsonData.language_id + '" ';
            connection.query($qa_info_sql, function (err, result2, fields) {
                if (err) throw err;
                var $qa_info_sql_data = result2;
                $qa_info_sql_data_transform = {};
                if ($qa_info_sql_data.length > 0) {
                    for (var i = 0; i < $qa_info_sql_data.length; i++) {
                        $qa_info_sql_data_transform[$qa_info_sql_data[i].name] = $qa_info_sql_data[i].value;
                    }
                }
                var $attendee_info_sql = 'SELECT * FROM `conf_attendees` LEFT JOIN conf_attendees_info ON conf_attendees.id = conf_attendees_info.attendee_id where conf_attendees.`id` = "' + $qa_sql_data[0].attendee_id + '" AND languages_id = "' + jsonData.language_id + '"';
                connection.query($attendee_info_sql, function (err, result3, fields) {
                    if (err) throw err;
                    var $attendee_info_sql_data = result3;
                    $attendee_info_sql_data_transform = {};
                    if ($attendee_info_sql_data.length > 0) {
                        for (var i = 0; i < $attendee_info_sql_data.length; i++) {
                            $attendee_info_sql_data_transform[$attendee_info_sql_data[i].name] = $attendee_info_sql_data[i].value;
                            $attendee_info_sql_data_transform['first_name'] = $attendee_info_sql_data[i].first_name;
                            $attendee_info_sql_data_transform['last_name'] = $attendee_info_sql_data[i].last_name;
                            $attendee_info_sql_data_transform['image'] = $attendee_info_sql_data[i].image;
                        }
                    }
                    var $speaker_info_sql = 'SELECT * FROM `conf_attendees` LEFT JOIN conf_attendees_info ON conf_attendees.id = conf_attendees_info.attendee_id where conf_attendees.`id` = "' + $qa_sql_data[0].speaker_id + '" AND languages_id = "' + jsonData.language_id + '" ';
                    connection.query($speaker_info_sql, function (err, result4, fields) {
                        if (err) throw err;
                        var $speaker_info_sql_data = result4;
                        $speaker_info_sql_data_transform = {};
                        if ($speaker_info_sql_data.length > 0) {
                            for (var i = 0; i < $speaker_info_sql_data.length; i++) {
                                $speaker_info_sql_data_transform[$speaker_info_sql_data[i].name] = $speaker_info_sql_data[i].value;
                                $speaker_info_sql_data_transform['first_name'] = $speaker_info_sql_data[i].first_name;
                                $speaker_info_sql_data_transform['last_name'] = $speaker_info_sql_data[i].last_name;
                                $speaker_info_sql_data_transform['image'] = $speaker_info_sql_data[i].image;
                            }
                        }
                        var $qa_likes_sql = 'SELECT * FROM `conf_qa_likes` WHERE `qa_id` = "' + $qa_sql_data[0].id + '" ';
                        connection.query($qa_likes_sql, function (err, result5, fields) {
                            if (err) throw err;
                            var $qa_likes_sql_data = result5;
                            var $qa_setting_sql = 'select * from `conf_qa_settings` where `deleted_at` is null and `event_id` = "' + jsonData.event_id + '" ';
                            connection.query($qa_setting_sql, function (err, resultSetting) {
                                if (err) throw err;
                                var $qa_setting_sql_data = resultSetting;
                                // Buisness logic START here
                                if (jsonData.ip == '39.61.51.233') {
                                    if (jsonData.enable_gdpr == '1') {
                                        if (jsonData.enable_gdpr == '1' && jsonData.enable_attendee_gdpr == '1') {
                                            var result = socket_recent_popler_render_HTML($qa_sql_data[0], $qa_info_sql_data_transform, $attendee_info_sql_data_transform, $qa_setting_sql_data, 1);
                                        } else {
                                            if (jsonData.attendee_invisible == '1') {
                                                var result = '';
                                            } else {
                                                var result = socket_recent_popler_render_HTML($qa_sql_data[0], $qa_info_sql_data_transform, $attendee_info_sql_data_transform, $qa_setting_sql_data, 0);
                                            }
                                        }
                                    } else {
                                        var result = socket_recent_popler_render_HTML($qa_sql_data[0], $qa_info_sql_data_transform, $attendee_info_sql_data_transform, $qa_setting_sql_data, 1);
                                    }
                                } else {
                                    var result = socket_recent_popler_render_HTML($qa_sql_data[0], $qa_info_sql_data_transform, $attendee_info_sql_data_transform, $qa_setting_sql_data, 1);
                                }
                                QAListingAction_socket(result, jsonData.agenda_id);
                                // Buisness logic End here
                            });
                        });
                    });
                });
            });
        }
    });
}

function socket_recent_popler_render_HTML($qa_sql_data, $qa_info_sql_data_transform, $attendee_info_sql_data_transform, $qa_setting_sql_data, status) {
    var html = '<li class="ui-li ui-li-static ui-btn-up-a ui-li-has-thumb ui-last-child ' + $qa_sql_data.id + '"><div style="overflow: hidden;">';
    html += '<span class="qa-post-time"><img src="' + jsonData.base_url + '_mobile_assets/images/wall_ic_time.svg" alt="likes"><time datetime=" ' + dateFormat(jsonData.created_at, "d  mmm. yy HH:MM") + '" > ' + dateFormat(jsonData.created_at, "d  mmm. yy HH:MM") + ' </time></span>';
    if ($qa_sql_data.anonymous_user == 0) {
        html += '<a href="" class="ui-link-inherit">';
        if ($attendee_info_sql_data_transform.image && status == 1) {
            html += '<img src="' + jsonData.base_url + 'assets/attendees/' + $attendee_info_sql_data_transform.image + '" width = "60px" />';
        } else {
            html += '<img src="' + jsonData.base_url + 'images/speakers/no-img.jpg" width = "60px" />';
        }
        html += '<h2 class="ui-li-heading">' + $attendee_info_sql_data_transform.initial + ' ' + $attendee_info_sql_data_transform.first_name + ' ' + $attendee_info_sql_data_transform.last_name + '</h2>';
        if (status == 1) {
            html += '<p class="ui-li-desc">' + $attendee_info_sql_data_transform.title + ' ' + $attendee_info_sql_data_transform.company_name; + '</p>';
        }
        html += '</a>';
    } else {
        html += '<a href="" class="ui-link-inherit">';
        html += '<img src="' + jsonData.base_url + 'images/speakers/no-img.jpg" width = "60px" />';
        html += ' <h2 class="ui-li-heading">Anonymous</h2>';
        html += '</a>';
    }
    html += '</div>';
    html += '<div class="qa-question"><p class="ui-li-desc">' + $qa_info_sql_data_transform.question + '</p></div>';
    if ($qa_setting_sql_data[0].up_vote == '1') {
        html += '<div>';
        html += '<a href="javascript:void(0);" class="links like-post qa-like-post" id="like-container-' + $qa_sql_data.id + '" data-id="' + $qa_sql_data.id + '" data data-role="none">';
        html += '<div class="icon-links"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="21" height="21" viewBox="0 0 21 21"> <defs> <style>.cls-1{fill: #737373; fill-rule: evenodd;}</style> </defs> <path d="M15.500,10.000 L13.300,7.000 C12.200,5.400 11.500,3.600 11.100,1.700 L10.700,-0.000 L8.500,-0.000 C7.100,-0.000 6.000,1.100 6.000,2.500 L6.000,3.100 C6.000,3.900 6.100,4.700 6.300,5.500 L7.000,8.000 L2.700,8.000 C1.300,8.000 0.100,9.000 0.000,10.400 C0.000,10.900 0.100,11.400 0.400,11.800 C0.100,12.200 0.000,12.700 0.000,13.200 C0.000,14.100 0.400,14.800 1.100,15.300 C1.000,15.400 1.000,15.600 1.000,15.800 C1.000,16.800 1.600,17.700 2.500,18.100 C2.500,18.300 2.500,18.400 2.500,18.500 C2.500,19.900 3.600,21.000 5.000,21.000 L8.800,21.000 C9.500,21.000 10.100,20.900 10.700,20.800 L13.800,20.000 L19.000,20.000 L19.000,10.000 L15.500,10.000 ZM17.000,18.000 L14.500,18.000 L11.100,18.800 C10.600,18.900 10.100,19.000 9.600,19.000 L4.900,19.000 C4.400,19.000 4.000,18.600 4.000,18.100 L4.000,18.000 L4.200,17.000 L3.200,16.500 C2.900,16.400 2.700,16.100 2.700,15.700 C2.700,15.600 2.700,15.600 2.700,15.500 L2.900,14.600 L2.100,14.100 C1.900,13.900 1.800,13.600 1.800,13.300 C1.800,13.100 1.800,13.000 1.900,12.800 L2.400,12.100 L1.900,11.400 C1.800,11.200 1.700,11.100 1.800,10.900 C1.800,10.400 2.200,10.000 2.800,10.000 L9.400,10.000 L8.200,5.700 C8.000,4.900 7.900,4.100 7.900,3.300 L7.900,2.400 C7.900,2.100 8.100,1.900 8.400,1.900 L9.500,1.900 C10.000,4.100 10.800,6.300 12.000,8.100 L14.700,12.000 L17.000,12.000 L17.000,18.000 Z" transform="translate(1)" class="cls-1"/></svg></div>';
        html += '<span class="btn-links" id="like-label-' + $qa_sql_data.id + '">Upvote</span>';
        html += '<span id="like-count-' + $qa_sql_data.id + '">0</span>';
        html += '</a></div>';
    }
    html += '</li>';
    return html;
}

function QAListingAction_socket($data, $agenda_id) {
    $data2 = {
        'event': 'qa_admin_block_listing_' + jsonData.event_id + '_' + jsonData.agenda_id,
        'data': {
            'data_info': $data
        }
    };
    $json_string = JSON.stringify($data2);
    redis.publish('event-buizz', $json_string);
}

function isEmpty(obj) {
    for (var key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
            return false;
        }
    }
    return true;
}
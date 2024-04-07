import React, { useEffect, useState, useRef } from "react";
import Input from "components/forms/Input";
import TextArea from "components/forms/TextArea";
import DateTime from "components/forms/DateTime";
import DropDown from "components/forms/DropDown";
import Select from "react-select";
import Image from 'next/image'
import {
  fetchProfileData,
  profileSelector,
  updateProfileData,
  cleanRedirect
} from "store/Slices/myAccount/profileSlice";
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector, useDispatch } from "react-redux";
import moment from "moment";
import PageLoader from "components/ui-components/PageLoader";
import { useRouter } from 'next/router';

const Selectstyles = {
  control: base => ({
    ...base,
    height: 38,
    minHeight: 38,
    backgroundColor: 'transparent',
    border: 'none',
    width: '90%',
    maxWidth: '90%',
    marginTop: 6,
    marginLeft: 3,
    "&:focus": {
      borderColor: "red"
    }
  })
};
const Selectstyles2 = {
  control: base => ({
    ...base,
    height: 50,
    minHeight: 50,
    width: '100%',
    maxWidth: '100%',
    marginBottom: 10,

  })
};

const MyProfileEdit = () => {

  const { event } = useSelector(eventSelector);

  const dispatch = useDispatch();

  useEffect(() => {
    dispatch(fetchProfileData(event.id, event.url, 1));
  }, []);

  const { attendee_edit, languages, callingCodes, countries, loading, alert, error, settings, labels, redirect, customFields, attendee_module_labels } =
    useSelector(profileSelector);

  return (
    attendee_edit ? (
      <ProfileEditForm
        attendee={attendee_edit}
        languages={languages}
        callingCodes={callingCodes}
        countries={countries}
        event={event}
        loading={loading}
        alert={alert}
        error={error}
        settings={settings}
        labels={labels}
        redirect={redirect}
        customFields={customFields}
        attendeeLabels={attendee_module_labels}
      />) : <PageLoader />

  );
};

export default MyProfileEdit;

const ProfileEditForm = ({ attendee, languages, callingCodes, countries, event, loading, alert, error, settings, labels, redirect, customFields, attendeeLabels }) => {

  const dispatch = useDispatch();

  const [attendeeData, setAttendeeData] = useState(attendee);

  const [customFieldData, setCustomFieldData] = useState(customFields.reduce((ack1, question, i)=>{
       let answers = attendee.info[`custom_field_id${question.event_id}`].split(',').reduce((ack2, id, i)=>{ 
          let is_answer = question.children_recursive.find((answer)=>(answer.id == id));
          if(is_answer !== undefined){
            ack2.push({
              label: is_answer.name,
              value: is_answer.id,
            });
          }
          return ack2;
        }, []);
        ack1[`custom_field_id_q${i}`] = question.allow_multiple === 1 ? answers : answers[0];
        return ack1;
    }, {}));

  const userInfo = localStorage.getItem(`event${event.id}User`);

  const isAuthenticated = userInfo !== undefined && userInfo !== null ? JSON.parse(userInfo) : {};

  const router = useRouter();

  const mounted = useRef(false);

  const inputFileRef = React.useRef();

  const inputresumeFileRef = React.useRef();

  useEffect(() => {
    mounted.current = true;
    return () => { mounted.current = false; };
  }, []);

  useEffect(() => {
    setAttendeeData({
      ...attendeeData,
      SPOKEN_LANGUAGE: languages
        .filter(
          (item) =>
            attendeeData.SPOKEN_LANGUAGE && attendeeData.SPOKEN_LANGUAGE.length > 0 && attendeeData.SPOKEN_LANGUAGE.split(",").indexOf(item.name) !== -1
        )
        .map((item, index) => ({
          label: item.name,
          value: item.id,
          key: index,
        })),
      calling_code: {
        label: attendeeData.phone && attendeeData.phone.split("-")[0],
        value: attendeeData.phone && attendeeData.phone.split("-")[0],
      },
      phone: attendeeData.phone && attendeeData.phone.split("-")[1],
      gdpr: attendeeData.phone && attendeeData.current_event_attendee.gdpr,
      country: countries.reduce((ack, item) => { if (item.id == attendeeData.info.country) { return { label: item.name, value: item.id } } return ack; }, {}),
      info: {
        ...attendeeData.info,
        private_country: countries.reduce((ack, item) => { if (item.id == attendeeData.info.private_country) { return { label: item.name, value: item.id } } return ack; }, {}),
      },
    });
  }, []);

  const updateAttendeeFeild = (e) => {
    const { name, value } = e.currentTarget;
    setAttendeeData({
      ...attendeeData,
      [name]: value,
    });
  };

  const updateAttendeeInfoFeild = (e) => {
    const { name, value } = e.currentTarget;
    setAttendeeData({
      ...attendeeData,
      info: {
        ...attendeeData.info,
        [name]: value,
      },
    });
  };

  const updateDate = (obj) => {
    setAttendeeData({
      ...attendeeData,
      [obj.name]: (typeof obj.item === 'object' && obj.item !== null) ? obj.item.format("YYYY-MM-DD") : obj.item,
    });

  };

  const updateInfoDate = (obj) => {
    setAttendeeData({
      ...attendeeData,
      info: {
        ...attendeeData.info,
        [obj.name]: (typeof obj.item === 'object' && obj.item !== null) ? obj.item.format("YYYY-MM-DD") : obj.item,
      },
    });

  };

  const updateSelect = (obj) => {
    setAttendeeData({
      ...attendeeData,
      [obj.name]: obj.item,
    });
  };

  const updateInfoSelect = (obj) => {
    setAttendeeData({
      ...attendeeData,
      info: {
        ...attendeeData.info,
        [obj.name]: obj.item,
      },
    });
  };
  
  const updateCustomFieldSelect = (obj) => {
    setCustomFieldData({
      ...customFieldData,
      [obj.name]: obj.item,
    });
  };

  const updateAttendee = (e) => {
    e.preventDefault();

    let attendeeObj = {
      phone: `${attendeeData?.calling_code?.value}-${attendeeData?.phone}`,
    };

    let custom_field_id = customFields.reduce((ack, question, i)=>{
      if(customFieldData[`custom_field_id_q${i}`] !== undefined){
         let ids =question.allow_multiple === 1 ? customFieldData[`custom_field_id_q${i}`].map((ans)=>(ans.value)).join(',') + "," : customFieldData[`custom_field_id_q${i}`].value +',';
          ack += ids;
      }
      return ack;
    }, '');

    let infoObj = {
      ...attendeeData.info,
      country: attendeeData?.country ? attendeeData?.country?.value : attendeeData?.info?.country,
      private_country: attendeeData?.info?.private_country?.value,
      
    }

    infoObj[`custom_field_id${event.id}`] = custom_field_id;

    console.log(infoObj)

    let settings = {
      gdpr: attendeeData.gdpr
    }


    if (attendeeData.email) attendeeObj.email = attendeeData.email;
    if (attendeeData.first_name) attendeeObj.first_name = attendeeData.first_name;
    if (attendeeData.last_name) attendeeObj.last_name = attendeeData.last_name;
    if (attendeeData.FIRST_NAME_PASSPORT) attendeeObj.FIRST_NAME_PASSPORT = attendeeData.FIRST_NAME_PASSPORT;
    if (attendeeData.LAST_NAME_PASSPORT) attendeeObj.LAST_NAME_PASSPORT = attendeeData.LAST_NAME_PASSPORT;
    if (attendeeData.BIRTHDAY_YEAR) attendeeObj.BIRTHDAY_YEAR = attendeeData.BIRTHDAY_YEAR;
    if (attendeeData.EMPLOYMENT_DATE) attendeeObj.EMPLOYMENT_DATE = attendeeData.EMPLOYMENT_DATE;
    if (attendeeData.image) attendeeObj.image = attendeeData.image;
    if (attendeeData.file) attendeeObj.file = attendeeData.file;
    if (attendeeData.attendee_cv) attendeeObj.att_cv = attendeeData.attendee_cv;
    if (attendeeData.SPOKEN_LANGUAGE) attendeeObj.SPOKEN_LANGUAGE = attendeeData.SPOKEN_LANGUAGE.reduce((ack, item, index) => {
      if (index !== attendeeData.SPOKEN_LANGUAGE.length - 1) {
        return ack += `${item.label},`
      }
      return ack += `${item.label}`
    }, "");

    const data = {
      attendeeObj,
      settings,
      infoObj
    };
    dispatch(updateProfileData(event.id, event.url, data));
  };

  useEffect(() => {
    dispatch(cleanRedirect(''))
    if (redirect !== '' && redirect !== null && mounted.current) {
      setTimeout(() => {
        router.push(`/${event.url}/profile`);
      }, 1000)
    }
  }, [redirect])

  return (
    <div className="edgtf-container ebs-my-profile-area pb-5">
      <div className="edgtf-container-inner container">
        <div className="ebs-header">
          <h2>Edit profile</h2>
        </div>
        <form onSubmit={(e) => updateAttendee(e)}>
          <div
            style={{ background: "transparent" }}
            className="ebs-my-account-container"
          >
            <div className="ebs-edit-profile-section">
              {/* <h3 className="ebs-title">Basic Information:</h3> */}
              {settings.map((setting, index)=>(
                <React.Fragment key={index}>
                  {setting?.name === 'initial'  && (
                    <Input
                      label={labels?.initial}
                      name="initial"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.initial}
                    />
                  )}
                  {setting?.name === 'first_name' && (<Input
                    label={labels?.first_name}
                    placeholder="First name"
                    name="first_name"
                    required={true}
                    readOnly={setting?.is_editable === 1 ? false : true}
                    onChange={(e) => {
                      updateAttendeeFeild(e);
                    }}
                    value={attendeeData.first_name}
                  />)}
                  {setting?.name === 'last_name' && (<Input
                    label={labels?.last_name}
                    name="last_name"
                    readOnly={setting?.is_editable === 1 ? false : true}
                    onChange={(e) => {
                      updateAttendeeFeild(e);
                    }}
                    placeholder="Last name"
                    value={attendeeData.last_name}
                  />)}
                  
                  {setting?.name === 'bio_info' && (
                    <TextArea
                      label={labels?.about}
                      name="about"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      placeholder="about"
                      value={attendeeData.info.about}
                    />
                  )}
                  {setting?.name === 'age'  && (
                    <Input
                      label={labels?.age}
                      name="age"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.age}
                    />
                  )}
                  {setting?.name === 'gender' && (
                    <div className="inline radio-check-field style-radio radio-feild">
                      <h5>{labels?.gender}</h5>
                      <label>
                        <input
                          type="radio"
                          name="gender"
                          value="male"
                          onChange={(e) => {
                            if (setting?.is_editable === 1) {
                              updateAttendeeInfoFeild(e);
                            }
                          }}
                          checked={attendeeData.info.gender === "male"}
                        />
                        <span>Male</span>
                      </label>
                      <label>
                        <input
                          type="radio"
                          name="gender"
                          value="female"
                          onChange={(e) => {
                            if (setting?.is_editable === 1) {
                              updateAttendeeInfoFeild(e);
                            }
                          }}
                          checked={attendeeData.info.gender === "female"}
                        />
                        <span>Female</span>
                      </label>
                    </div>
                  )}
                  {setting?.name === 'birth_date' && (
                    <DateTime
                      label={labels?.BIRTHDAY_YEAR}
                      required={true}
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(item) => {
                        updateDate({ item, name: "BIRTHDAY_YEAR" });
                      }}
                      value={attendeeData.BIRTHDAY_YEAR !== '' && attendeeData.BIRTHDAY_YEAR !== '0000-00-00' && attendeeData.BIRTHDAY_YEAR !== '0000-00-00 00:00:00' ? moment(attendeeData.BIRTHDAY_YEAR).format('YYYY-MM-DD') : ''}
                      showdate={"YYYY-MM-DD"}
                    />
                  )}
                  {setting?.name === 'first_name_passport' && (
                    <Input
                      label={labels?.FIRST_NAME_PASSPORT}
                      name="FIRST_NAME_PASSPORT"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeFeild(e);
                      }}
                      value={attendeeData.FIRST_NAME_PASSPORT}
                    />
                  )}
                  {setting?.name === 'last_name_passport' && (
                    <Input
                      label={labels?.LAST_NAME_PASSPORT}
                      name="LAST_NAME_PASSPORT"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeFeild(e);
                      }}
                      value={attendeeData.LAST_NAME_PASSPORT}
                    />
                  )}
                  {setting?.name === 'place_of_birth' && (
                    <Input
                      label={labels?.place_of_birth}
                      readOnly={setting?.is_editable === 1 ? false : true}
                      name="place_of_birth"
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.place_of_birth}
                    />
                  )}
                  {setting?.name === 'passport_no' && (
                    <Input
                      label={labels?.passport_no}
                      name="passport_no"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.passport_no}
                    />
                  )}
                  {setting?.name === 'date_of_issue_passport' && (
                    <DateTime
                      label={labels?.date_of_issue_passport}
                      readOnly={setting?.is_editable === 1 ? false : true}
                      required={true}
                      onChange={(item) => {
                        updateInfoDate({ item, name: "date_of_issue_passport" });
                      }}
                      value={attendeeData.info.date_of_issue_passport !== '' && attendeeData.info.date_of_issue_passport !== '0000-00-00' && attendeeData.info.date_of_issue_passport !== '0000-00-00 00:00:00' ? moment(attendeeData.info.date_of_issue_passport).format('YYYY-MM-DD') : ''}
                      showdate={"YYYY-MM-DD"}
                    />
                  )}
                  {setting?.name === 'date_of_expiry_passport'&& (
                    <DateTime
                      label={labels?.date_of_expiry_passport}
                      readOnly={setting?.is_editable === 1 ? false : true}
                      required={true}
                      onChange={(item) => {
                        updateInfoDate({ item, name: "date_of_expiry_passport" });
                      }}
                      value={
                        attendeeData.info.date_of_expiry_passport !== '' && attendeeData.info.date_of_expiry_passport !== '0000-00-00' && attendeeData.info.date_of_expiry_passport !== '0000-00-00 00:00:00' ? moment(attendeeData.info.date_of_expiry_passport).format('YYYY-MM-DD') : ''
                      }
                      showdate={"YYYY-MM-DD"}
                    />
                  )}
  
                  {setting?.name === 'spoken_languages' && (
                    <DropDown
                      label={labels?.SPOKEN_LANGUAGE}
                      listitems={languages}
                      required={false}
                      isDisabled={setting?.is_editable === 1 ? false : true}
                      isMulti={true}
                      selected={
                        attendeeData.SPOKEN_LANGUAGE &&
                          typeof attendeeData.SPOKEN_LANGUAGE !== String
                          ? attendeeData.SPOKEN_LANGUAGE
                          : null
                      }
                      name="SPOKEN_LANGUAGE"
                      onChange={(item) => {
                        updateSelect({ item, name: "SPOKEN_LANGUAGE" });
                      }}
                    />
                  )}
                  {setting?.name === 'profile_picture' && (
                    <div className="ebs-profile-image" onClick={() => {
                      inputFileRef.current.click();
                    }}>
                      <label>
                        {((attendeeData && attendeeData?.image && attendeeData?.image !== "") || attendeeData?.blob_image !== undefined) ? (
                          <img src={`${attendeeData?.blob_image !== undefined ? attendeeData?.blob_image : process.env.NEXT_APP_EVENTCENTER_URL +
                            "/assets/attendees/" +
                            attendeeData?.image}`} alt="" />
                        ) : (
                          <img src="https://via.placeholder.com/155.png" alt="" />
                        )}
                        {setting?.is_editable === 1 && (
                          <>
                            <span>{attendeeLabels?.ATTENDEE_PROFILE_PICTURE}</span>
                          </>
                        )}
                      </label>
                      {setting?.is_editable === 1 && (
                        <input type="file" style={{ display: 'none' }} ref={inputFileRef} onChange={(e) => {
                          if (e.target.files.length > 0) {
                            setAttendeeData({
                              ...attendeeData,
                              file: e.target.files[0],
                              blob_image: URL.createObjectURL(e.target.files[0]),
                            });
                          }
                        }} />
                      )}
                    </div>
                  )}
                  {setting?.name === 'resume' && (
                    <div className="ebs-profile-image" >
                      <label>
                        {((attendeeData && attendeeData?.attendee_cv && attendeeData?.attendee_cv !== "")) ? (
                          <>
                            {(typeof attendeeData.attendee_cv === 'string')  ? <a className="attendee_cv_link" href={process.env.NEXT_APP_EVENTCENTER_URL + '/event/' + event.url +'/settings/downloadResume/' + attendeeData?.attendee_cv}>
                              <img style={{borderRadius:0}} src={`${process.env.NEXT_APP_EVENTCENTER_URL +
                                '/_admin_assets/images/pdf512.png'}`} alt="" />
                            </a> : <img style={{borderRadius:0}} src={`${process.env.NEXT_APP_EVENTCENTER_URL +
                                '/_admin_assets/images/pdf512.png'}`} alt="" />
                            }
                          </>
                        ) : (
                          <img src="https://via.placeholder.com/155.png" alt="" />
                        )}
                        {setting?.is_editable === 1 && (
                          <>
                            <span onClick={() => {
                              inputresumeFileRef.current.click();
                            }}>
                              {attendeeLabels?.ATTENDEE_RESUME}
                            </span>
                          </>
                        )}
                      </label>
                      {setting?.is_editable === 1 && (
                        <input type="file" style={{ display: 'none' }} ref={inputresumeFileRef} onChange={(e) => {
                          if (e.target.files.length > 0) {
                            setAttendeeData({
                              ...attendeeData,
                              attendee_cv: e.target.files[0],
                            });
                          }
                        }} />
                      )}
                    </div>
                  )}
                  {setting?.name === 'company_name' && (
                    <Input
                      label={labels?.company_name}
                      name="company_name"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.company_name}
                    />
                  )}
                  {setting?.name === 'title' && (
                    <Input
                      label={labels?.title}
                      name="title"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={
                        attendeeData.info &&
                        attendeeData.info.title &&
                        attendeeData.info.title
                      }
                    />
                  )}
                  {setting?.name === 'organization' && (
                    <Input
                      label={labels?.organization}
                      name="organization"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.organization}
                    />
                  )}
                  {setting?.name === 'employment_date' && (
                    <DateTime
                      label={labels?.EMPLOYMENT_DATE}
                      readOnly={setting?.is_editable === 1 ? false : true}
                      required={true}
                      onChange={(item) => {
                        updateDate({ item, name: "EMPLOYMENT_DATE" });
                      }}
                      value={attendeeData.EMPLOYMENT_DATE !== '' && attendeeData.EMPLOYMENT_DATE !== '0000-00-00' && attendeeData.EMPLOYMENT_DATE !== '0000-00-00 00:00:00' ? moment(attendeeData.EMPLOYMENT_DATE).format('YYYY-MM-DD') : ''}
                      showdate={"YYYY-MM-DD"}
                    />
                  )}
                  {setting?.name === 'department' && (
                    <Input
                      label={labels?.department}
                      name="department"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.department}
                    />
                  )}
                  {setting?.name === 'country' && (
                    <Select
                      styles={Selectstyles2}
                      isDisabled={setting?.is_editable === 1 ? false : true}
                      placeholder={labels?.country}
                      components={{ IndicatorSeparator: null }}
                      options={countries.map((item, index) => {
                        return {
                          label: item.name,
                          value: item.id,
                          key: index,
                        };
                      })}
                      value={attendeeData.country}
                      onChange={(item) => {
                        updateSelect({ item, name: "country" });
                      }}
                    />
                  )}
                  {setting?.name === 'show_industry' && (
                    <Input
                      label={labels?.industry}
                      name="industry"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.industry}
                    />
                  )}
                  {setting?.name === 'show_job_tasks' && (
                    <Input
                      label={labels?.jobs}
                      name="jobs"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.jobs}
                    />
                  )}
                  {setting?.name === 'interest' && (
                    <Input
                      label={labels?.interests}
                      name="interests"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.interests}
                    />
                  )}
                  {setting?.name === 'network_group' && (
                    <Input
                      label={labels?.network_group}
                      name="network_group"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.network_group}
                    />
                  )}
                  {setting?.name === 'delegate_number' && (
                    <Input
                      label={labels?.delegate}
                      name="delegate_number"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.delegate_number}
                    />
                  )}
                  {setting?.name === 'table_number' && (
                    <Input
                      label={labels?.table_number}
                      name="table_number"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.table_number}
                    />
                  )}
                 
                  {setting?.name === 'pa_street' && (
                    <>
                      <Input
                        label={labels?.private_street}
                        name="private_street"
                        readOnly={setting?.is_editable === 1 ? false : true}
                        onChange={(e) => {
                          updateAttendeeInfoFeild(e);
                        }}
                        value={attendeeData.info.private_street}
                      />
                    </>
                  )}
                  {setting?.name === 'pa_house_no' && (
                    <Input
                      label={labels?.private_house_number}
                      name="private_house_number"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.private_house_number}
                    />
                  )}
                  {setting?.name === 'pa_post_code' && (
                    <Input
                      label={labels?.private_post_code}
                      name="private_post_code"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.private_post_code}
                    />
                  )}
                  {setting?.name === 'pa_city' && (
                    <Input
                      label={labels?.private_city}
                      name="private_city"
                      readOnly={setting?.is_editable === 1 ? false : true}
                      onChange={(e) => {
                        updateAttendeeInfoFeild(e);
                      }}
                      value={attendeeData.info.private_city}
                    />
                  )}
                  {setting?.name === 'pa_country' && (
                    <Select
                      styles={Selectstyles2}
                      isDisabled={setting?.is_editable === 1 ? false : true}
                      placeholder={labels?.private_country}
                      components={{ IndicatorSeparator: null }}
                      options={countries.map((item, index) => {
                        return {
                          label: item.name,
                          value: item.id,
                          key: index,
                        };
                      })}
                      value={attendeeData?.info?.private_country}
                      onChange={(item) => {
                        updateInfoSelect({ item, name: "private_country" });
                      }}
                    />
                  )}
                  {setting?.name === 'show_custom_field' && (
                      customFields.map((question, i)=>(
                        <React.Fragment key={question.id}>
                        <Select
                          styles={Selectstyles2}
                          isDisabled={setting?.is_editable === 1 ? false : true}
                          placeholder={question.name}
                          components={{ IndicatorSeparator: null }}
                          options={question.children_recursive.map((item, index) => {
                            return {
                              label: item.name,
                              value: item.id,
                              key: index,
                            };
                          })}
                          value={customFieldData[`custom_field_id_q${i}`] !== undefined ? customFieldData[`custom_field_id_q${i}`] : null}
                          isMulti={question.allow_multiple === 1 ? true : 0}
                          onChange={(item) => {
                            console.log(item);
                            updateCustomFieldSelect({ item, name: `custom_field_id_q${i}` });
                          }}
                        />
                        </React.Fragment>
                      ))
                    )}

              {setting?.name === 'phone' &&
                  <div className="ebs-contact-row d-flex">
                    <div style={{ width: 55, height: 55, position: 'relative', marginRight: 5 }}>
                      <Image objectFit='contain' layout="fill" src={require("public/img/ico-phone.svg")} alt="" /></div>
                    <div className="form-phone-field" style={{width:'100%'}}>
                      {attendeeData.calling_code && (
                        <React.Fragment>
                          <div style={{ minWidth: "108px" }}>
                            <Select
                              styles={Selectstyles}
                              className="w-full h-full"
                              placeholder=".."
                              components={{ IndicatorSeparator: null }}
                              options={callingCodes.map((item, index) => {
                                return {
                                  label: item.name,
                                  value: item.id,
                                  key: index,
                                };
                              })}
                              value={
                                attendeeData.calling_code !== undefined && {
                                  label: attendeeData.calling_code.label,
                                  value: attendeeData.calling_code.value,
                                }
                              }
                              onChange={(item) => {
                                updateSelect({ item, name: "calling_code" });
                              }}
                            />
                          </div>
                        </React.Fragment>
                      )}
                      <div style={{ width: "75%" }}>
                        <Input
                          label={labels?.phone}
                          name="phone"
                          readOnly={setting?.is_editable === 1 ? false : true}
                          onChange={(e) => {
                            updateAttendeeFeild(e);
                          }}
                          value={attendeeData.phone}
                        />
                      </div>
                    </div>
                  </div>}
                  
                


                {setting?.name === 'email' && (
                  <div className="ebs-contact-row d-flex">
                    <div style={{ width: 55, height: 55, position: 'relative', marginRight: 5 }}><Image objectFit='contain' layout="fill" src={require("public/img/ico-envelope.svg")} alt="" /></div>
                    <Input
                      label={labels?.email}
                      required
                      name="email"
                      readOnly={true}
                      onChange={(e) => {
                        updateAttendeeFeild(e);
                      }}
                      value={attendeeData.email}
                    />
                  </div>
                )}
                </React.Fragment>
              ))}
              <div className="ebs-contact-info">
                <div className="ebs-contact-row d-flex">
                  <div style={{ width: 55, height: 55, position: 'relative', marginRight: 5 }}><Image objectFit='contain' layout="fill" src={require("public/img/ico-web.svg")} alt="" /></div>
                  <Input
                    label="Website"
                    required
                    name="website"
                    onChange={(e) => {
                      updateAttendeeInfoFeild(e);
                    }}
                    value={attendeeData.info.website}
                  />
                </div>
                <div className="ebs-contact-row d-flex">
                  <div style={{ width: 55, height: 55, position: 'relative', marginRight: 5 }}><Image objectFit='contain' layout="fill" src={require("public/img/ico-facebook.svg")} alt="" /></div>
                  <Input
                    label="Facebook"
                    required
                    name="facebook"
                    onChange={(e) => {
                      updateAttendeeInfoFeild(e);
                    }}
                    value={attendeeData.info.facebook}
                  />
                </div>
                <div className="ebs-contact-row d-flex">
                  <div style={{ width: 55, height: 55, position: 'relative', marginRight: 5 }}><Image objectFit='contain' layout="fill" src={require("public/img/ico-twitter.svg")} alt="" /></div>
                  <Input
                    label="Twitter"
                    required
                    name="twitter"
                    onChange={(e) => {
                      updateAttendeeInfoFeild(e);
                    }}
                    value={attendeeData.info.twitter}
                  />
                </div>
                <div className="ebs-contact-row d-flex">
                  <div style={{ width: 55, height: 55, position: 'relative', marginRight: 5 }}><Image objectFit='contain' layout="fill" src={require("public/img/ico-linkedin.svg")} alt="" /></div>
                  <Input
                    label="Linkedin"
                    required
                    name="linkedin"
                    onChange={(e) => {
                      updateAttendeeInfoFeild(e);
                    }}
                    value={attendeeData.info.linkedin}
                  />
                </div>
              </div>
              {attendee.gdpr !== undefined && (
                <div className="radio-check-field ebs-radio-lg field-terms-services">
                  <label>
                    <input
                      type="checkbox"
                      name="gdpr"
                      value={attendeeData.gdpr}
                      onChange={(e) =>
                        updateSelect({ name: "gdpr", item: !attendeeData.gdpr })
                      }
                      checked={attendeeData.gdpr}
                    />
                    <span>
                      I agree to the <mark>GDPR Terms of Service</mark>
                    </span>
                  </label>
                </div>
              )}
            </div>
          </div>
          <div className="bottom-button">
            <input className="btn btn-save-next btn-loader" type="submit" value={loading && attendee !== null ? "updating..." : "Update"} />
          </div>
        </form>
      </div>
    </div>
  );

};

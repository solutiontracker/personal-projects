import React from 'react'
import { useGlobalState, useGlobalMutation } from '@app/sdks/agora/utils/container'
import useDevices from '@app/sdks/agora/utils/use-devices'
import PropTypes from 'prop-types'
import { makeStyles } from '@material-ui/core/styles'
import FormControl from '@material-ui/core/FormControl'
import Select from '@material-ui/core/Select'
import InputLabel from '@material-ui/core/InputLabel'
import Box from '@material-ui/core/Box'
import MenuItem from '@material-ui/core/MenuItem'
import { Link } from 'react-router-dom'

SettingsCard.propTypes = {
  name: PropTypes.string,
  resolution: PropTypes.string,
  cameraDevice: PropTypes.string,
  microphoneDevice: PropTypes.string,
  video: PropTypes.bool,
  audio: PropTypes.bool
}

const useStyles = makeStyles(theme => ({
  menuTitle: {
    color: '#333333',
    textAlign: 'center',
    fontSize: 'h6.fontSize',
    position: 'relative',
    top: '7px'
  },
  marginTop: {
    marginTop: '0 !important'
  },
  menu: {
    margin: '0.4rem 0',
    position: 'relative',
    height: '39px',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center'
  },
  line: {
    marginTop: '0.2rem',
    marginBottom: '0.5rem',
    borderBottom: '1px solid #EAEAEA'
  },
  hr: {
    borderBottom: '1px solid #EAEAEA'
  },
  switchItem: {
    flexDirection: 'row-reverse !important',
    marginLeft: '0 !important',
    marginRight: '0 !important',
    justifyContent: 'space-between'
  }
}))

export default function SettingsCard() {
  const classes = useStyles()

  const stateCtx = useGlobalState()

  const mutationCtx = useGlobalMutation()

  const [cameraList, microphoneList] = useDevices()

  return (
    <Box flex="1" display="flex" flexDirection="column">
      <Link to="/" className='back-btn' />
      <Box display="flex" flex="1" flexDirection="column" padding="0 1rem" justifyContent="flex-start">
        <FormControl className={classes.menu}>
          <span className={classes.menuTitle}>Setting</span>
        </FormControl>
        <div className={classes.line}></div>
        {/* <FormControl>
          <InputLabel htmlFor="resolution">Resolution</InputLabel>
          <Select
            value={stateCtx.config.resolution}
            onChange={(evt) => {
              mutationCtx.updateConfig({
                resolution: evt.target.value
              })
            }}
            inputProps={{
              name: 'resolution',
              id: 'resolution'
            }}
          >
            <MenuItem value={'480p'}>480p</MenuItem>
            <MenuItem value={'720p'}>720p</MenuItem>
            <MenuItem value={'1080p'}>1080p</MenuItem>
          </Select>
        </FormControl>
        <FormControl>
          <InputLabel htmlFor="codec">Video Codec</InputLabel>
          <Select
            value={stateCtx.codec}
            onChange={(evt) => {
              mutationCtx.setCodec(evt.target.value)
            }}
            inputProps={{
              name: 'codec',
              id: 'codec'
            }}
          >
            <MenuItem value={'h264'}>h264</MenuItem>
            <MenuItem value={'vp8'}>vp8</MenuItem>
          </Select>
        </FormControl> */}
        <FormControl>
          <InputLabel htmlFor="camera">Camera</InputLabel>
          <Select
            value={stateCtx.config.cameraId}
            onChange={(evt) => {
              mutationCtx.updateConfig({
                cameraId: evt.target.value
              })
            }}
            inputProps={{
              name: 'camera',
              id: 'camera'
            }}
          >
            {cameraList.map(
              (item, key) =>
                <MenuItem
                  key={key}
                  value={item.value}
                >{item.label}</MenuItem>
            )}
          </Select>
        </FormControl>
        <FormControl>
          <InputLabel htmlFor="Microphone">Microphone</InputLabel>
          <Select
            value={stateCtx.config.microphoneId}
            onChange={(evt) => {
              mutationCtx.updateConfig({
                microphoneId: evt.target.value
              })
            }}
            inputProps={{
              name: 'microphone',
              id: 'microphone'
            }}
          >
            {microphoneList.map(
              (item, key) =>
                <MenuItem
                  key={key}
                  value={item.value}
                >{item.label}</MenuItem>
            )}
          </Select>
        </FormControl>
        {/* <FormControl>
          <FormControlLabel
            control={
              <CustomSwitch
                checked={stateCtx.muteVideo}
                onChange={() => {
                  mutationCtx.setVideo(!stateCtx.muteVideo)
                }}
                value={stateCtx.muteVideo}
                color="primary"
              />
            }
            className={classes.switchItem}
            label="Video"
          />
          <div className={classes.hr}></div>
        </FormControl>
        <FormControl>
          <FormControlLabel
            control={
              <CustomSwitch
                checked={stateCtx.muteAudio}
                onChange={() => {
                  mutationCtx.setAudio(!stateCtx.muteAudio)
                }}
                value={stateCtx.muteAudio}
                color="primary"
              />
            }
            className={classes.switchItem}
            label="Audio"
          />
          <div className={classes.hr}></div>
        </FormControl>
        <FormControl>
          <FormControlLabel
            control={
              <CustomSwitch
                checked={stateCtx.profile}
                onChange={() => {
                  mutationCtx.setProfile(!stateCtx.profile)
                }}
                value={stateCtx.profile}
                color="primary"
              />
            }
            className={classes.switchItem}
            label="Profile"
          />
          <div className={classes.hr}></div>
        </FormControl> */}
      </Box>
    </Box>
  )
}

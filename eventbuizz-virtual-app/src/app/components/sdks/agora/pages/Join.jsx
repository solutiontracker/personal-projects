import React, { useEffect } from 'react'
import { makeStyles } from '@material-ui/core/styles'
import FormControl from '@material-ui/core/FormControl'
import Box from '@material-ui/core/Box'
import Button from '@material-ui/core/Button'
import { useGlobalState, useGlobalMutation } from '@app/sdks/agora/utils/container'
import { Container } from '@material-ui/core'
import Tooltip from '@material-ui/core/Tooltip'

const useStyles = makeStyles(theme => ({
    fontStyle: {
        color: '#9ee2ff'
    },
    bottomStyle: {
        color: '#9ee2ff',
        position: 'absolute',
        bottom: '20px',
        alignSelf: 'center'
    },
    midItem: {
        marginTop: '1rem',
        marginBottom: '6rem'
    },
    item: {
        flex: 1,
        display: 'flex',
        alignItems: 'center'
    },
    coverLeft: {
        background: 'linear-gradient(to bottom, #307AFF, 50%, #46cdff)',
        alignItems: 'center',
        flex: 1,
        display: 'flex',
        flexDirection: 'column'
    },
    coverContent: {
        display: 'flex',
        justifyContent: 'center',
        flexDirection: 'column',
        color: '#fff'
    },
    container: {
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        height: '100%',
        width: '100%',
        minWidth: '100%',
        minHeight: '100%',
        boxSizing: 'border-box',
    },
    card: {
        display: 'flex',
        minWidth: 700,
        minHeight: 500,
        maxHeight: 500,
        borderRadius: '10px',
        boxShadow: '0px 6px 18px 0px rgba(0,0,0,0.2)'
    },
    input: {
        maxWidth: '250px',
        minWidth: '250px',
        alignSelf: 'center'
    },
    grid: {
        margin: '0 !important'
    },
    button: {
        lineHeight: '21px',
        color: 'rgba(255,255,255,1)',
        fontSize: '17px',
        textTransform: 'none',
        height: '44px',
        width: '180px',
        '&:hover': {
            backgroundColor: '#82C2FF'
        },
        margin: theme.spacing(1),
        marginTop: '33px',
        backgroundColor: '#44a2fc',
        borderRadius: '30px'
    },
    radio: {
        padding: '0',
        fontSize: '14px',
        // display: 'flex',
        alignItems: 'center',
        paddingRight: '5px'
    }
}))

export default function Join(props) {
    const classes = useStyles()

    const stateCtx = useGlobalState()

    const mutationCtx = useGlobalMutation()

    const handleClick = () => {
        props.history.push(`/event/${props.event.url}/agora/join-video-meeting/${props.video_id}/${props.channelName}/${props.userRole}/1`)
    }

    useEffect(() => {
        let mounted = true;
        if (stateCtx.loading === true && mounted) {
            mutationCtx.stopLoading()
        }
        return () => mounted = false;
    }, [stateCtx.loading, mutationCtx])

    return (
        <Container maxWidth="sm" className={`${classes.container} video-block ${props.userRole !== 'audience' ? 'join-meeting' : 'join-meeting-audience'}`}>
            <Box flex="1" display="flex" alignItems="center" justifyContent="flex-start" flexDirection="column">
                <Box marginTop="92" flex="1" display="flex" alignItems="center" justifyContent="center" flexDirection="column">
                    <FormControl className={classes.grid}>
                        {props.userRole === 'audience' ? (
                            <Tooltip title='Play'>
                                <img style={{ filter: 'grayscale(1)', cursor: 'pointer' }} width="70" src={require('images/youtube-play.png')} alt="" onClick={handleClick} />
                            </Tooltip>
                        ) : (
                                <Button style={{ backgroundColor: props.event.settings.primary_color }} onClick={handleClick} variant="contained" color="primary" className={classes.button}>
                                    {props.event.labels.DESKTOP_APP_LABEL_JOIN}
                                </Button>
                            )}
                    </FormControl>
                </Box>
            </Box>
        </Container >
    )
}

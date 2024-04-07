import React from 'react'
import { makeStyles } from '@material-ui/core/styles'
import CircularProgress from '@material-ui/core/CircularProgress'

const useStyles = makeStyles(theme => ({
  progress: {
    margin: theme.spacing(2),
    color: '#44A2FC'
  },
  container: {
    height: '100%',
    width: '100%',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    background: '#666666',
    position: 'fixed'
  }
}))

export default function CircularLoading () {
  const classes = useStyles()

  return (
    <div className={`${classes.container} stream-loader`}>
      <CircularProgress className={classes.progress} />
    </div>
  )
}

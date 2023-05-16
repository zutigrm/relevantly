import React, {useState, useEffect} from 'react';
import apiFetch from '@wordpress/api-fetch';
import NavTabs from '../components/tabs/tabs';
import Snackbar from '@mui/material/Snackbar';
import Backdrop from '@mui/material/Backdrop';
import CircularProgress from '@mui/material/CircularProgress';
import MuiAlert, { AlertProps } from '@mui/material/Alert';
import Paper from '@mui/material/Paper';

import GeneralSettings from './general';
import PhrasesSettings from './phrases';

const Alert = React.forwardRef (function Alert(
  props,
  ref,
) {
  return <MuiAlert elevation={6} ref={ref} variant="filled" {...props} />;
});

function Dashboard() {
  const [ apiData, setApiData ] = useState([]);
  const [ activeTab, setActiveTab ] = useState('general');
  const [ loader, setLoader ] = useState(false);
  const [ notice, setNotice ] = useState(false);

  useEffect(() => {
    apiFetch( {
      path: '/relevantly/v1/settings',
      method: 'GET',
    } )
      .then((data) => {
        setApiData(data.data);
      });
  }, []);

  const updateSettings = ( settingKey, settingValue ) => {
    apiFetch( {
      path: '/relevantly/v1/settings',
      method: 'POST',
      data: { setting_key: settingKey, setting_value: settingValue },
    } )
      .then( ( response ) => {
        if ( response?.data && ! response?.data?.details ) {
          setApiData(response.data);
          setNotice({
            message: response.message,
            severity: 'success'
          });
        
        } else {
          setNotice({
            message: response.message,
            severity: 'error'
          });
        } 

        setLoader(false);
      } )
      .catch( ( error ) => {
        console.error( 'Error updating setting:', error );
        setNotice({
          message: 'Error updating setting',
          severity: 'error'
        });
        setLoader(false);
      } );
  }

  const handleNoticeClose = (event, reason) => {
    if (reason === 'clickaway') {
      return;
    }

    setNotice(false);
  };

  return (
    <div>
      <h1>Relevantly Dashboard</h1>
      <NavTabs active={activeTab} setActiveTab={setActiveTab} />
      <Paper elevation={1}>
        <GeneralSettings 
          storeData={apiData?.storeData}
          relatedEnabled={apiData?.relatedEnabled}
          wooEnabled={apiData?.wooEnabled}
          active={activeTab} 
          updateSettings={updateSettings}
          setLoader={setLoader}
        />
        <PhrasesSettings 
          active={activeTab}
        />
      </Paper>
       <Backdrop
        sx={{ color: '#fff', zIndex: (theme) => theme.zIndex.drawer + 1 }}
        open={loader}
      >
         <CircularProgress color="inherit" />
      </Backdrop>
      <Snackbar 
        open={notice ? true : false} 
        autoHideDuration={5000} 
        onClose={handleNoticeClose}
        anchorOrigin={{ 
          vertical: 'bottom',
          horizontal: 'right'
        }}
      >
        <Alert onClose={handleNoticeClose} severity={notice?.severity} sx={{ width: '100%' }}>
          {notice?.message}
        </Alert>
      </Snackbar>
    </div>
  );
}

export default Dashboard;
import React, {useState, useEffect} from 'react';
import FormGroup from '@mui/material/FormGroup';
import FormHelperText from '@mui/material/FormHelperText';
import FormControlLabel from '@mui/material/FormControlLabel';
import Switch, { SwitchProps } from '@mui/material/Switch';
import TextField from '@mui/material/TextField';

const GeneralSettings = ({ 
    storeData, 
    relatedEnabled,
    wooEnabled,
    active,
    updateSettings,
    setLoader
}) => {
    
    const handleChange = (event) => {
        updateSettings(event.target.name, event.target.checked ? '1' : '');
        setLoader(true);
      };

    return (
        <div 
            className={`relevantly-content-tab relevantly-general ${active === 'general' ? 'active' : ''}`}
        >
            <FormGroup>
                <FormControlLabel
                    control={
                        <Switch checked={storeData ? true : false} onChange={handleChange} name="storeData" />
                    }
                    label="Generate Post Phases"
                />
                <FormHelperText className='relevantly-space-small'>Enable this to run the background process which will extract phases for matching on all existing posts. Then on creating new post, updating or deleting, data will be modified accordingly as well.</FormHelperText>

                <FormControlLabel
                    control={
                        <Switch checked={relatedEnabled ? true : false} onChange={handleChange} name="relatedEnabled" />
                    }
                    label="Enable Related Posts Globally"
                />
                <FormHelperText className='relevantly-space-small'>Include related content section after blog posts. It will be displayed at the bottom of the contnet</FormHelperText>

                {relatedEnabled ?
                    <>
                        <TextField id="limit" label="Related Posts Limit" type="number" variant="standard" />
                        <FormHelperText className='relevantly-space-small'>Set how many related items you want to show globally under posts.</FormHelperText>
                    </> 
                        : null
                }

                <FormControlLabel
                    control={
                        <Switch checked={wooEnabled ? true : false} onChange={handleChange} name="relatedEnabled" />
                    }
                    label="Enable Woo Related Products"
                />
                <FormHelperText className='relevantly-space-small'>Include related products section after woo products posts. It will replace the default logic of showing related products. <strong>Note: you need to have WooCommerce activated on your website for this option to work</strong> </FormHelperText>
            </FormGroup>
        </div>
    );
}

export default GeneralSettings;
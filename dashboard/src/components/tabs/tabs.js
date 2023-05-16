import React, {useState} from 'react';
import NavTab from '../tab/tab';

const NavTabs = ({ active, setActiveTab }) => {

    return (
        <div
            className='relevantly-tabs__navs' 
        >
            <NavTab 
                label="General"
                name="general"
                active={active}
                clickCallback={setActiveTab}
            />
            <NavTab 
                label="Extracted Phrases"
                name="phrases"
                active={active}
                clickCallback={setActiveTab}
            />
        </div>
    )
}

export default NavTabs;
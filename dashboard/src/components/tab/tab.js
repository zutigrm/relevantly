import React from 'react';

const NavTab = ({ 
    name,
    active, 
    label, 
    clickCallback 
}) => (
    <a 
        className={`relevantly-tabs__navs-item ${active === name ? 'active' : ''}`} 
        onClick={() => clickCallback(name)}
    >
        {label}
    </a>
)

export default NavTab;
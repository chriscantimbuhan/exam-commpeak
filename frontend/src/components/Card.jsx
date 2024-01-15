import React from 'react';

const Card = ({ children }) => {
    return (
        <React.Fragment>
            <div className='card border border-2 mb-3'>
                {children}
            </div>
        </React.Fragment>
    );
};

export default Card;
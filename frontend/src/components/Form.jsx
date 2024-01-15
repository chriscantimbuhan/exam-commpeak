import React from 'react';

const Form = ({ id, children, encType }) => {
    return (
        <React.Fragment>
            <div className='card border border-light-subtle'>
                <div className='card-body'>
                    <form id={id} encType={encType}>
                        {children}
                    </form>
                </div>
            </div>
        </React.Fragment>
    );
};

export default Form;

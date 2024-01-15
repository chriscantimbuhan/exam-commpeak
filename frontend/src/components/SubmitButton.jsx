import React from 'react';

const SubmitButton = ({ value }) => {
    return (
        <React.Fragment>
            <input type="submit" className="btn btn-primary" value={value} />
        </React.Fragment>
    );
}

export default SubmitButton;
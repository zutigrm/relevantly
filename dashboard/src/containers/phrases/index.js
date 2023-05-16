import React, {useState, useEffect} from 'react';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Backdrop from '@mui/material/Backdrop';
import CircularProgress from '@mui/material/CircularProgress';
import Paper from '@mui/material/Paper';
import TablePagination from '@mui/material/TablePagination';

const PhrasesSettings = ({ 
    active,
}) => {
    const [ phrases, setPhrases ] = useState(null);
    const [ page, setPage ] = React.useState(0);
    const [ rowsPerPage, setRowsPerPage ] = useState(20);

    let queryParams = { page: 1, limit: 20 }

    useEffect(() => {
        apiFetch( { 
            path: addQueryArgs( '/relevantly/v1/phrases', queryParams ) 
        } ).then( ( phases ) => {
            console.log( phases );
            if ( phases && phases?.data ) {
                setPhrases( phases.data );
            }
        } );
    }, [] );


    const handleChangePage = (event, newPage) => {
        setPage(newPage);
        let queryParams = { page: newPage, limit: 20 }

        apiFetch( { 
            path: addQueryArgs( '/relevantly/v1/phrases', queryParams ) 
        } ).then( ( phases ) => {
            console.log( phases );
            if ( phases && phases?.data ) {
                setPhrases( phases.data );
            }
        } );
    };

    const handleChangeRowsPerPage = (event) => {
        setRowsPerPage(parseInt(event.target.value, 10));
        setPage(0);
    };

    if ( null === phrases && active == 'phrases' ) {
       return (
            <Backdrop
                sx={{ color: '#fff', zIndex: (theme) => theme.zIndex.drawer + 1 }}
                open={true}
            >
                <CircularProgress color="inherit" />
            </Backdrop>
       );
    }

    return (
        <div 
            className={`relevantly-content-tab relevantly-phrases ${active === 'phrases' ? 'active' : ''}`}
        >
            <TableContainer component={Paper}>
                <Table sx={{ minWidth: 650 }} size="small" aria-label="Extracted Phrases">
                    <TableHead>
                    <TableRow>
                        <TableCell>ID</TableCell>
                        <TableCell align="right">Post ID</TableCell>
                        <TableCell align="right">Keyword</TableCell>
                    </TableRow>
                    </TableHead>
                    <TableBody>
                    {phrases?.items.map((row) => (
                        <TableRow
                            key={row.name}
                            sx={{ '&:last-child td, &:last-child th': { border: 0 } }}
                        >
                            <TableCell component="th" scope="row">
                                {row.id}
                            </TableCell>
                            <TableCell align="right">{row.post_id}</TableCell>
                            <TableCell align="right">{row.keyword}</TableCell>
                        </TableRow>
                    ))}
                    </TableBody>
                </Table>
            </TableContainer>
            <TablePagination
                rowsPerPageOptions={[10, 20, 50]}
                component="div"
                count={phrases?.total ? Math.ceil( phrases?.total / 20 ) : 1}
                rowsPerPage={rowsPerPage}
                page={page}
                onPageChange={handleChangePage}
                onRowsPerPageChange={handleChangeRowsPerPage}
            />
        </div>
    );
};

export default PhrasesSettings;

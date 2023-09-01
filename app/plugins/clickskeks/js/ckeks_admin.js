
document.addEventListener("DOMContentLoaded", function(event) {
    const keksForm = document.getElementById('ckeksform');
    const resetBtn = document.getElementById('reset');
    const submitBtn = document.getElementById(('submit_code'));
    
    resetBtn.addEventListener('click', function(){
        document.querySelector('input[name=submit_type]').value = 'reset';
    });
    
    submitBtn.addEventListener('click', function(){
        document.querySelector('input[name=submit_type]').value = 'submit';
    });
    
    keksForm.addEventListener('submit', function(e){
    
        e.preventDefault();
        
        if ( document.querySelector('input[name=submit_type]').value == 'reset' ) {
            if( confirm('Sind Sie sicher?') )
            {
                keksForm.submit();
            }
        }else{
            keksForm.submit();
        }
    });
});
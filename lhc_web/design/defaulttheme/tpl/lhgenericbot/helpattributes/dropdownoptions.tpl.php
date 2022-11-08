<?php if ($context == 'dropdownoptions') : ?>
    <ul>
        <li>Option name || &lt;department_id OR department_alias&gt;</li>
        <li>Internal Value => Option name || &lt;department_id OR department_alias&gt;</li>
        <li>If you are passing department make sure. This is highly recommended as chat might not be able to start if you have custom required fields by department.
            <ul>
                <li>You pass it for all options</li>
                <li>All options have to be assigned to same start chat configuration</li>
                <li>You have chosen that department in department options. <span class="badge badge-info">Apply this configuration also to these departments</span> or <span class="badge badge-info">Department</span></li>
            </ul>
        </li>
    </ul>
<?php endif; ?>
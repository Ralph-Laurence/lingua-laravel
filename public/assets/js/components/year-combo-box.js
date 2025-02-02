class YearComboBox
{
    static currentYear = new Date().getFullYear();
    static minYear = 1980;

    static generateYearOptions(startYear, endYear)
    {
        let options = '';
        endYear = endYear || minYear;
        startYear = startYear || currentYear;

        for (let year = startYear; year >= endYear; year--) {
            options += `<option value="${year}">${year}</option>`;
        }
        return options;
    };

    static initialize()
    {
        $.each($('.year-select'), function(index, element)
        {
            let value = $(element).attr('data-value');

            if (value && value.trim() !== "")
                $(element).val(value).selectmenu();

            else
                $(element).selectmenu();
        });
    };
}

$(document).ready(function()
{
    YearComboBox.initialize();
});

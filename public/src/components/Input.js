import view from "../core/view.js";

export default async function (props, ctx) {

    if(props.field.ajax)
    {
        const response = await ctx.http.get(props.field.ajax.url)
        const mapping = props.field.ajax.response;

        props.field.options = response.data.map(item => {
            const result = {
                id: item.id
            };

            for (const key in mapping) {
                result[key] = item[mapping[key]];
            }

            return result;
        })
    }

    props.field.key = props.field.keyValue ?? props.field.name

    return view.render('components/fields/' + props.field.type, {
        field: props.field,
        data: props.data
    })
}
import view from "../core/view.js";

export default function (props, ctx) {

    return view.render('components/breadcrumb', {
        breadcrumbs: props.breadcrumbs,
        title: props.title
    })
}
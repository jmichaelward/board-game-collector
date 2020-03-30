/* global wp */
import { Autocomplete } from "@wordpress/components";

const {
	apiFetch,
} = wp;

const Edit = (props) => {
	const {
		isSelected,
		attributes,
	} = props;

	const {
		title,
		url,
		thumbnail,
	} = attributes;

	if ( isSelected ) {
		return (
			<p>{JSON.stringify(props)}</p>
		)
	}

	return (
		<div>
			<a href={url} title={title}>
				<div>
					<img src={thumbnail} alt={title}/>
					<p>{title}</p>
				</div>
			</a>
		</div>
	);
};

export default Edit;

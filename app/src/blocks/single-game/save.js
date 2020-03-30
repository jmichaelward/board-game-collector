const Save = (props) => {
	const {
		attributes: {
			title,
			url,
			thumbnail
		}
	} = props;

	return (
		<div>
			<a href={url} title={title}>
				<div>
					<img src={thumbnail} title={title}/>
					<p>{title}</p>
				</div>
			</a>
		</div>
	)
};

export default Save;

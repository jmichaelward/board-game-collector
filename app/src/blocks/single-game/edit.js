/* global wp */

const {
	element: {
		useEffect
	},
	editor: {
		URLInputButton,
	},
} = wp;

const Edit = (props) => {
	const {
		className,
		setAttributes,
		isSelected,
		attributes: {
			text,
			url,
			gameId,
		}
	} = props;

	const onChangeContent = ( url, post ) => {
		setAttributes( { url, text: (post && post.title) || '', gameId: (post && post.id) || null } );
	};

	useEffect(() => {

	});

	return (
		<div>
			{isSelected &&
				<>
					<URLInputButton
						url={ url }
						onChange={ onChangeContent }
					/>
				</>
			}

			<div className={className}>
				<p className="game-title"
						data-id={gameId}
						data-url={url}
				><a href={url}>{text}</a></p>
			</div>
		</div>
	)
};

export default Edit;

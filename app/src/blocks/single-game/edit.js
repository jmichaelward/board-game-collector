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
			title,
			url,
			gameId,
		}
	} = props;

	const onChangeContent = ( url, post ) => {
		setAttributes( { url, title: (post && post.title) || '', gameId: (post && post.id) || null } );
	};

	useEffect(() => {

	});

	return (
		<div className={className}>
			{isSelected &&
				<>
					<URLInputButton
						url={ url }
						onChange={ onChangeContent }
					/>
				</>
			}

			<div className="board-game-collector-single-game__details" data-id={gameId}>
				<p className="board-game-collector-single-game__title">
					<a className="board-game-collector-single-game__link" href={url}>{title}</a>
				</p>
			</div>
		</div>
	)
};

export default Edit;

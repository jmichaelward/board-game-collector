/* global wp */

const Save = (props) => {
	const {
		className,
		attributes: {
			url,
			title,
			gameId
		}
	} = props;

	return (
		<div className={className}>
			<div className="board-game-collector-single-game__details" data-id={gameId}>
				<p className="board-game-collector-single-game__title">
					<a className="board-game-collector-single-game__link" href={url}>{title}</a>
				</p>
			</div>
		</div>
	)
};

export default Save;

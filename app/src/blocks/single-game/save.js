/* global wp */

const Save = (props) => {
	const {
		className,
		attributes: {
			url,
			text,
			gameId
		}
	} = props;

	return (
		<div className={className}>
			<p className="game-title" data-id={gameId} data-url={url}>
				<a href={url}>{text}</a></p>
		</div>
	)
};

export default Save;

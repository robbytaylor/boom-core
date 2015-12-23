class Chunk {
	private urlPrefix: string = '/cms/chunk/';

	constructor(private pageId: number, private type: string, private slotname: string) {
		this.pageId = pageId;
		this.type = type;
		this.slotname = slotname;

		this.urlPrefix = this.urlPrefix + this.pageId + '/';
	}

	/**
	 * To remove a chunk save it with no data.
	 *
	 */
	delete(template: string) {
		return this.save({
			'template': template
		});
	}

	save(data) {
		data.slotname = this.slotname;
		data.type = this.type;

		return $.post(this.urlPrefix + 'save', data);
	}
}